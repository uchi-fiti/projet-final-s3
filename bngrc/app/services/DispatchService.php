<?php
namespace app\services;
use PDO;
use Exception;

class DispatchService
{
    public static function execute(PDO $pdo)
    {
        try {
            $pdo->beginTransaction();

            $stmtDon = $pdo->prepare("
                SELECT * FROM bngrc_dons
                WHERE quantite_restante > 0
                ORDER BY date_saisie ASC
            ");
            $stmtDon->execute();
            $dons = $stmtDon->fetchAll(PDO::FETCH_ASSOC);

            foreach ($dons as $don) {

                $donRestant = $don['quantite_restante'];

                $stmtBesoin = $pdo->prepare("
                    SELECT * FROM bngrc_besoins
                    WHERE type_id = :type_id
                    AND quantite_restante > 0
                    ORDER BY date_saisie ASC
                ");

                $stmtBesoin->execute([
                    'type_id' => $don['type_id']
                ]);

                $besoins = $stmtBesoin->fetchAll(PDO::FETCH_ASSOC);

                foreach ($besoins as $besoin) {

                    if ($donRestant <= 0) {
                        break;
                    }

                    $besoinRestant = $besoin['quantite_restante'];

                    $quantiteAttribuee = min($donRestant, $besoinRestant);

                    $stmtInsert = $pdo->prepare("
                        INSERT INTO bngrc_attributions 
                        (besoin_id, don_id, quantite_attribuee, date_attribution)
                        VALUES (:besoin_id, :don_id, :quantite, NOW())
                    ");
                    $stmtInsert->execute([
                        'besoin_id' => $besoin['id'],
                        'don_id' => $don['id'],
                        'quantite' => $quantiteAttribuee
                    ]);

                    $stmtUpdateBesoin = $pdo->prepare("
                        UPDATE bngrc_besoins
                        SET quantite_restante = quantite_restante - :quantite
                        WHERE id = :id
                    ");
                    $stmtUpdateBesoin->execute([
                        'quantite' => $quantiteAttribuee,
                        'id' => $besoin['id']
                    ]);

                    $stmtUpdateDon = $pdo->prepare("
                        UPDATE bngrc_dons
                        SET quantite_restante = quantite_restante - :quantite
                        WHERE id = :id
                    ");
                    $stmtUpdateDon->execute([
                        'quantite' => $quantiteAttribuee,
                        'id' => $don['id']
                    ]);

                    $donRestant -= $quantiteAttribuee;
                }
            }

            $pdo->commit();

        } catch (Exception $e) {

            $pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Proportional Dispatch using Largest Remainder Method
     * Distributes donations proportionally across all matching besoins
     */
    public static function executeProportional(PDO $pdo)
    {
        try {
            $pdo->beginTransaction();

            // Load all dons with remaining quantity
            $stmtDon = $pdo->prepare("
                SELECT * FROM bngrc_dons
                WHERE quantite_restante > 0
                ORDER BY date_saisie ASC
            ");
            $stmtDon->execute();
            $dons = $stmtDon->fetchAll(PDO::FETCH_ASSOC);

            foreach ($dons as $don) {
                self::dispatchProportional($pdo, $don['id']);
            }

            $pdo->commit();

        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Dispatch a single don proportionally using Largest Remainder Method
     * 
     * @param PDO $pdo Database connection
     * @param int $don_id The donation ID to dispatch
     * @param bool $useTransaction Whether to use its own transaction (false when called from executeProportional)
     */
    public static function dispatchProportional(PDO $pdo, int $don_id, bool $useTransaction = false)
    {
        try {
            if ($useTransaction) {
                $pdo->beginTransaction();
            }

            // Step 1: Load the don
            $stmtDon = $pdo->prepare("
                SELECT * FROM bngrc_dons
                WHERE id = :id AND quantite_restante > 0
            ");
            $stmtDon->execute(['id' => $don_id]);
            $don = $stmtDon->fetch(PDO::FETCH_ASSOC);

            if (!$don) {
                // No donation found or no remaining quantity
                if ($useTransaction) {
                    $pdo->commit();
                }
                return;
            }

            $donQuantiteRestante = (int) $don['quantite_restante'];

            // Step 2: Load all compatible besoins (same type_id, quantite_restante > 0)
            $stmtBesoin = $pdo->prepare("
                SELECT * FROM bngrc_besoins
                WHERE type_id = :type_id
                AND quantite_restante > 0
                ORDER BY date_saisie ASC
            ");
            $stmtBesoin->execute(['type_id' => $don['type_id']]);
            $besoins = $stmtBesoin->fetchAll(PDO::FETCH_ASSOC);

            if (empty($besoins)) {
                // No matching besoins
                if ($useTransaction) {
                    $pdo->commit();
                }
                return;
            }

            // Step 3: Calculate total besoins quantity
            $totalBesoins = 0;
            foreach ($besoins as $besoin) {
                $totalBesoins += (int) $besoin['quantite_restante'];
            }

            if ($totalBesoins <= 0) {
                if ($useTransaction) {
                    $pdo->commit();
                }
                return;
            }

            // Step 4: Apply Largest Remainder Method
            $allocations = [];
            
            foreach ($besoins as $besoin) {
                $besoinRestant = (int) $besoin['quantite_restante'];
                
                // Calculate exact proportional allocation
                $exact = $donQuantiteRestante * ($besoinRestant / $totalBesoins);
                $base = (int) floor($exact);
                $remainder = $exact - $base;
                
                // Cap base allocation to besoin's remaining quantity
                $base = min($base, $besoinRestant);
                
                $allocations[] = [
                    'besoin_id' => $besoin['id'],
                    'besoin_restant' => $besoinRestant,
                    'base' => $base,
                    'remainder' => $remainder,
                    'final' => $base
                ];
            }

            // Step 5: Calculate remaining units to distribute
            $usedBase = 0;
            foreach ($allocations as $alloc) {
                $usedBase += $alloc['base'];
            }
            $remaining = $donQuantiteRestante - $usedBase;

            // Step 6: Sort by remainder DESC and distribute remaining units
            usort($allocations, function($a, $b) {
                return $b['remainder'] <=> $a['remainder'];
            });

            $index = 0;
            while ($remaining > 0 && $index < count($allocations)) {
                // Check if this besoin can receive more
                if ($allocations[$index]['final'] < $allocations[$index]['besoin_restant']) {
                    $allocations[$index]['final']++;
                    $remaining--;
                }
                $index++;
            }

            // Step 7: Create attributions and update quantities
            $stmtInsert = $pdo->prepare("
                INSERT INTO bngrc_attributions 
                (besoin_id, don_id, quantite_attribuee, date_attribution)
                VALUES (:besoin_id, :don_id, :quantite, NOW())
            ");

            $stmtUpdateBesoin = $pdo->prepare("
                UPDATE bngrc_besoins
                SET quantite_restante = quantite_restante - :quantite
                WHERE id = :id
            ");

            $totalAttribue = 0;

            foreach ($allocations as $alloc) {
                $quantiteAttribuee = $alloc['final'];
                
                if ($quantiteAttribuee <= 0) {
                    continue; // Skip if nothing to allocate
                }

                // Ensure we don't allocate more than besoin needs
                $quantiteAttribuee = min($quantiteAttribuee, $alloc['besoin_restant']);
                
                // Ensure we don't exceed what's left in the don
                if ($totalAttribue + $quantiteAttribuee > $donQuantiteRestante) {
                    $quantiteAttribuee = $donQuantiteRestante - $totalAttribue;
                }

                if ($quantiteAttribuee <= 0) {
                    continue;
                }

                // Insert attribution
                $stmtInsert->execute([
                    'besoin_id' => $alloc['besoin_id'],
                    'don_id' => $don_id,
                    'quantite' => $quantiteAttribuee
                ]);

                // Update besoin
                $stmtUpdateBesoin->execute([
                    'quantite' => $quantiteAttribuee,
                    'id' => $alloc['besoin_id']
                ]);

                $totalAttribue += $quantiteAttribuee;
            }

            // Update don's remaining quantity
            if ($totalAttribue > 0) {
                $stmtUpdateDon = $pdo->prepare("
                    UPDATE bngrc_dons
                    SET quantite_restante = quantite_restante - :quantite
                    WHERE id = :id
                ");
                $stmtUpdateDon->execute([
                    'quantite' => $totalAttribue,
                    'id' => $don_id
                ]);
            }

            if ($useTransaction) {
                $pdo->commit();
            }

        } catch (Exception $e) {
            if ($useTransaction) {
                $pdo->rollBack();
            }
            throw $e;
        }
    }
}
