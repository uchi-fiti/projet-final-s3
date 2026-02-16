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
                SELECT * FROM dons
                WHERE quantite_restante > 0
                ORDER BY date_saisie ASC
            ");
            $stmtDon->execute();
            $dons = $stmtDon->fetchAll(PDO::FETCH_ASSOC);

            foreach ($dons as $don) {

                $donRestant = $don['quantite_restante'];

                $stmtBesoin = $pdo->prepare("
                    SELECT * FROM besoins
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
                        INSERT INTO attributions 
                        (besoin_id, don_id, quantite_attribuee, date_attribution)
                        VALUES (:besoin_id, :don_id, :quantite, NOW())
                    ");
                    $stmtInsert->execute([
                        'besoin_id' => $besoin['id'],
                        'don_id' => $don['id'],
                        'quantite' => $quantiteAttribuee
                    ]);

                    $stmtUpdateBesoin = $pdo->prepare("
                        UPDATE besoins
                        SET quantite_restante = quantite_restante - :quantite
                        WHERE id = :id
                    ");
                    $stmtUpdateBesoin->execute([
                        'quantite' => $quantiteAttribuee,
                        'id' => $besoin['id']
                    ]);

                    $stmtUpdateDon = $pdo->prepare("
                        UPDATE dons
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
}
