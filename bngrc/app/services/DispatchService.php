<?php
namespace app\services;

use PDO;
use Exception;

class DispatchService
{
    private static function dispatch(PDO $pdo, string $mode = 'fifo')
    {
        try {
            $pdo->beginTransaction();

            // Get Argent type id
            $stmtArgent = $pdo->query("SELECT id FROM bngrc_types_besoins WHERE nom = 'Argent'");
            $argentTypeId = (int)$stmtArgent->fetchColumn();

            // Select donations with remaining values
            $stmtDon = $pdo->prepare("
                SELECT * FROM bngrc_dons
                WHERE 
                    (type_id = :argent AND montant_restant > 0)
                    OR
                    (type_id != :argent AND quantite_restante > 0)
                ORDER BY date_saisie ASC
            ");

            $stmtDon->execute(['argent' => $argentTypeId]);
            $dons = $stmtDon->fetchAll(PDO::FETCH_ASSOC);

            foreach ($dons as $don) {

                $isArgent = ((int)$don['type_id'] === $argentTypeId);

                if ($isArgent) {
                    self::dispatchMontant($pdo, $don, $mode);
                } else {
                    self::dispatchQuantite($pdo, $don, $mode);
                }
            }

            $pdo->commit();

        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    private static function dispatchMontant(PDO $pdo, array $don, string $mode)
    {
        $donRestant = (float)$don['montant_restant'];

        $order = ($mode === 'smallest')
            ? "montant_restant ASC"
            : "date_saisie ASC";

        $stmtBesoin = $pdo->prepare("
            SELECT * FROM bngrc_besoins
            WHERE type_id = :type_id
            AND montant_restant > 0
            ORDER BY $order
        ");

        $stmtBesoin->execute(['type_id' => $don['type_id']]);
        $besoins = $stmtBesoin->fetchAll(PDO::FETCH_ASSOC);

        foreach ($besoins as $besoin) {

            if ($donRestant <= 0) break;

            $besoinRestant = (float)$besoin['montant_restant'];
            $montantAttribue = min($donRestant, $besoinRestant);

            self::insertAttributionMontant($pdo, $besoin['id'], $don['id'], $montantAttribue);

            $pdo->prepare("
                UPDATE bngrc_besoins
                SET montant_restant = montant_restant - :m
                WHERE id = :id
            ")->execute([
                'm' => $montantAttribue,
                'id' => $besoin['id']
            ]);

            $donRestant -= $montantAttribue;
        }

        $pdo->prepare("
            UPDATE bngrc_dons
            SET montant_restant = :restant
            WHERE id = :id
        ")->execute([
            'restant' => $donRestant,
            'id' => $don['id']
        ]);
    }

    private static function dispatchQuantite(PDO $pdo, array $don, string $mode)
    {
        $donRestant = (int)$don['quantite_restante'];

        $order = ($mode === 'smallest')
            ? "quantite_restante ASC"
            : "date_saisie ASC";

        $stmtBesoin = $pdo->prepare("
            SELECT * FROM bngrc_besoins
            WHERE type_id = :type_id
            AND quantite_restante > 0
            ORDER BY $order
        ");

        $stmtBesoin->execute(['type_id' => $don['type_id']]);
        $besoins = $stmtBesoin->fetchAll(PDO::FETCH_ASSOC);

        foreach ($besoins as $besoin) {

            if ($donRestant <= 0) break;

            $besoinRestant = (int)$besoin['quantite_restante'];
            $quantiteAttribuee = min($donRestant, $besoinRestant);

            self::insertAttributionQuantite($pdo, $besoin['id'], $don['id'], $quantiteAttribuee);

            $pdo->prepare("
                UPDATE bngrc_besoins
                SET quantite_restante = quantite_restante - :q
                WHERE id = :id
            ")->execute([
                'q' => $quantiteAttribuee,
                'id' => $besoin['id']
            ]);

            $donRestant -= $quantiteAttribuee;
        }

        $pdo->prepare("
            UPDATE bngrc_dons
            SET quantite_restante = :restant
            WHERE id = :id
        ")->execute([
            'restant' => $donRestant,
            'id' => $don['id']
        ]);
    }

    private static function insertAttributionMontant(PDO $pdo, int $besoinId, int $donId, float $montant)
    {
        $pdo->prepare("
            INSERT INTO bngrc_attributions (besoin_id, don_id, montant_attribue)
            VALUES (:besoin_id, :don_id, :montant)
        ")->execute([
            'besoin_id' => $besoinId,
            'don_id' => $donId,
            'montant' => $montant
        ]);
    }

    private static function insertAttributionQuantite(PDO $pdo, int $besoinId, int $donId, int $quantite)
    {
        $pdo->prepare("
            INSERT INTO bngrc_attributions (besoin_id, don_id, quantite_attribuee)
            VALUES (:besoin_id, :don_id, :quantite)
        ")->execute([
            'besoin_id' => $besoinId,
            'don_id' => $donId,
            'quantite' => $quantite
        ]);
    }

    public static function executeFIFO(PDO $pdo)
    {
        self::dispatch($pdo, 'fifo');
    }

    public static function executeSmallestFirst(PDO $pdo)
    {
        self::dispatch($pdo, 'smallest');
    }

    /**
     * Proportional Dispatch using Largest Remainder Method
     * Distributes donations proportionally across all matching besoins
     */
    public static function executeProportional(PDO $pdo)
    {
        try {
            $pdo->beginTransaction();

            // Load all dons with remaining quantity/montant
            $argentType = $pdo->query("SELECT id FROM bngrc_types_besoins WHERE nom = 'Argent'")->fetch(PDO::FETCH_ASSOC);
            $argentTypeId = $argentType ? (int)$argentType['id'] : -1;

            $stmtDon = $pdo->prepare("
                SELECT * FROM bngrc_dons
                WHERE (type_id = :argent AND montant_restant > 0)
                   OR (type_id != :argent2 AND quantite_restante > 0)
                ORDER BY date_saisie ASC
            ");
            $stmtDon->execute(['argent' => $argentTypeId, 'argent2' => $argentTypeId]);
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

        // 1️⃣ Charger le don
        $stmtDon = $pdo->prepare("
            SELECT d.*, t.nom as type_nom
            FROM bngrc_dons d
            JOIN bngrc_types_besoins t ON d.type_id = t.id
            WHERE d.id = :id
        ");
        $stmtDon->execute(['id' => $don_id]);
        $don = $stmtDon->fetch(PDO::FETCH_ASSOC);

        if (!$don) return;

        $isArgent = ($don['type_nom'] === 'Argent');

        // Déterminer la valeur restante
        $donRestant = $isArgent
            ? (float)$don['montant_restant']
            : (int)$don['quantite_restante'];

        if ($donRestant <= 0) return;

        // 2️⃣ Charger besoins compatibles
        $stmtBesoin = $pdo->prepare("
            SELECT * FROM bngrc_besoins
            WHERE type_id = :type_id
            AND " . ($isArgent ? "montant_restant > 0" : "quantite_restante > 0") . "
        ");
        $stmtBesoin->execute(['type_id' => $don['type_id']]);
        $besoins = $stmtBesoin->fetchAll(PDO::FETCH_ASSOC);

        if (empty($besoins)) return;

        $totalBesoins = 0;
        foreach ($besoins as $b) {
            $totalBesoins += $isArgent
                ? (float)$b['montant_restant']
                : (int)$b['quantite_restante'];
        }

        if ($totalBesoins <= 0) return;

        $allocations = [];

        foreach ($besoins as $b) {

            $besoinRestant = $isArgent
                ? (float)$b['montant_restant']
                : (int)$b['quantite_restante'];

            $exact = $donRestant * ($besoinRestant / $totalBesoins);
            $base = floor($exact);
            $remainder = $exact - $base;

            $base = min($base, $besoinRestant);

            $allocations[] = [
                'besoin_id' => $b['id'],
                'besoin_restant' => $besoinRestant,
                'base' => $base,
                'remainder' => $remainder,
                'final' => $base
            ];
        }

        $used = array_sum(array_column($allocations, 'base'));
        $remaining = $donRestant - $used;

        usort($allocations, fn($a, $b) => $b['remainder'] <=> $a['remainder']);

        $i = 0;
        while ($remaining > 0 && $i < count($allocations)) {

            if ($allocations[$i]['final'] < $allocations[$i]['besoin_restant']) {
                $allocations[$i]['final'] += 1;
                $remaining -= 1;
            }

            $i++;
        }

        $totalAttribue = 0;

        foreach ($allocations as $alloc) {

            if ($alloc['final'] <= 0) continue;

            if ($isArgent) {

                $pdo->prepare("
                    INSERT INTO bngrc_attributions
                    (besoin_id, don_id, montant_attribue)
                    VALUES (:besoin_id, :don_id, :val)
                ")->execute([
                    'besoin_id' => $alloc['besoin_id'],
                    'don_id' => $don_id,
                    'val' => $alloc['final']
                ]);

                $pdo->prepare("
                    UPDATE bngrc_besoins
                    SET montant_restant = montant_restant - :val
                    WHERE id = :id
                ")->execute([
                    'val' => $alloc['final'],
                    'id' => $alloc['besoin_id']
                ]);

            } else {

                $pdo->prepare("
                    INSERT INTO bngrc_attributions
                    (besoin_id, don_id, quantite_attribuee)
                    VALUES (:besoin_id, :don_id, :val)
                ")->execute([
                    'besoin_id' => $alloc['besoin_id'],
                    'don_id' => $don_id,
                    'val' => $alloc['final']
                ]);

                $pdo->prepare("
                    UPDATE bngrc_besoins
                    SET quantite_restante = quantite_restante - :val
                    WHERE id = :id
                ")->execute([
                    'val' => $alloc['final'],
                    'id' => $alloc['besoin_id']
                ]);
            }

            $totalAttribue += $alloc['final'];
        }

        if ($isArgent) {
            $pdo->prepare("
                UPDATE bngrc_dons
                SET montant_restant = montant_restant - :val
                WHERE id = :id
            ")->execute([
                'val' => $totalAttribue,
                'id' => $don_id
            ]);
        } else {
            $pdo->prepare("
                UPDATE bngrc_dons
                SET quantite_restante = quantite_restante - :val
                WHERE id = :id
            ")->execute([
                'val' => $totalAttribue,
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
