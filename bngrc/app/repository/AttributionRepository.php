<?php

namespace app\repository;

use Flight;
use PDO;

class AttributionRepository {

    public function getAllAttributions() {
        $db = Flight::db();
        $stmt = $db->prepare("
            SELECT 
                a.id,
                d.description AS don_description,
                v.nom AS ville_nom,
                b.description AS besoin_description,
                a.quantite_attribuee,
                (a.quantite_attribuee * b.prix_unitaire) AS montant_attribue,
                a.date_attribution
            FROM bngrc_attributions a
            JOIN bngrc_besoins b ON a.besoin_id = b.id
            JOIN bngrc_dons d ON a.don_id = d.id
            JOIN bngrc_villes v ON b.ville_id = v.id
            ORDER BY a.date_attribution DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Revert the last batch (or last timestamp) of attributions.
     * Restores quantite_restante on besoins and dons, deletes the attributions
     * and records an audit entry in bngrc_action_logs.
     *
     * @param PDO|null $pdo Optional PDO instance for testing
     * @return bool
     * @throws \Exception
     */
    public function revertLastBatch(PDO $pdo = null)
    {
        $db = $pdo ?? Flight::db();
        $db->beginTransaction();

        // find the most recent attribution row
        $stmt = $db->prepare("SELECT batch_id, date_attribution FROM bngrc_attributions ORDER BY date_attribution DESC LIMIT 1");
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            $db->rollBack();
            throw new \Exception('Aucune attribution à annuler.');
        }

        if (!empty($row['batch_id'])) {
            $whereClause = 'batch_id = :ref';
            $params = ['ref' => $row['batch_id']];
            $logRef = $row['batch_id'];
        } else {
            $whereClause = 'date_attribution = :ref';
            $params = ['ref' => $row['date_attribution']];
            $logRef = $row['date_attribution'];
        }

        // restore besoins quantities (DB-agnostic; update per row)
        $stmt = $db->prepare("SELECT besoin_id, SUM(quantite_attribuee) AS q FROM bngrc_attributions WHERE $whereClause GROUP BY besoin_id");
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $updateBesoin = $db->prepare("UPDATE bngrc_besoins SET quantite_restante = CASE WHEN quantite_restante + :q > quantite THEN quantite ELSE quantite_restante + :q END WHERE id = :id");
        foreach ($rows as $r) {
            $updateBesoin->execute(['q' => $r['q'], 'id' => $r['besoin_id']]);
        }

        // restore dons quantities
        $stmt = $db->prepare("SELECT don_id, SUM(quantite_attribuee) AS q FROM bngrc_attributions WHERE $whereClause GROUP BY don_id");
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $updateDon = $db->prepare("UPDATE bngrc_dons SET quantite_restante = CASE WHEN quantite_restante + :q > quantite THEN quantite ELSE quantite_restante + :q END WHERE id = :id");
        foreach ($rows as $r) {
            $updateDon->execute(['q' => $r['q'], 'id' => $r['don_id']]);
        }

        // delete attributions for that batch/timestamp
        $stmt = $db->prepare("DELETE FROM bngrc_attributions WHERE $whereClause");
        $stmt->execute($params);

        // audit log (requires migration that creates bngrc_action_logs)
        $payload = json_encode(['ref' => $logRef, 'type' => 'revert_last']);
        $stmt = $db->prepare("INSERT INTO bngrc_action_logs (action, payload) VALUES (:action, :payload)");
        $stmt->execute(['action' => 'revert_attributions', 'payload' => $payload]);

        $db->commit();

        return true;
    }
} 
