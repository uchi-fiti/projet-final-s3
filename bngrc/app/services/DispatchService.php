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

            $stmtDon = $pdo->prepare("\n                SELECT * FROM bngrc_dons\n                WHERE quantite_restante > 0\n                ORDER BY date_saisie ASC\n            ");
            $stmtDon->execute();
            $dons = $stmtDon->fetchAll(PDO::FETCH_ASSOC);

            foreach ($dons as $don) {

                $donRestant = $don['quantite_restante'];

                $stmtBesoin = $pdo->prepare("\n                    SELECT * FROM bngrc_besoins\n                    WHERE type_id = :type_id\n                    AND quantite_restante > 0\n                    ORDER BY date_saisie ASC\n                ");

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

                    $stmtInsert = $pdo->prepare("\n                        INSERT INTO bngrc_attributions \n                        (besoin_id, don_id, quantite_attribuee, date_attribution)\n                        VALUES (:besoin_id, :don_id, :quantite, NOW())\n                    ");
                    $stmtInsert->execute([
                        'besoin_id' => $besoin['id'],
                        'don_id' => $don['id'],
                        'quantite' => $quantiteAttribuee
                    ]);

                    $stmtUpdateBesoin = $pdo->prepare("\n                        UPDATE bngrc_besoins\n                        SET quantite_restante = quantite_restante - :quantite\n                        WHERE id = :id\n                    ");
                    $stmtUpdateBesoin->execute([
                        'quantite' => $quantiteAttribuee,
                        'id' => $besoin['id']
                    ]);

                    $stmtUpdateDon = $pdo->prepare("\n                        UPDATE bngrc_dons\n                        SET quantite_restante = quantite_restante - :quantite\n                        WHERE id = :id\n                    ");
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
