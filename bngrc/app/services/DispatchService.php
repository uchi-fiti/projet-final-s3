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
}
