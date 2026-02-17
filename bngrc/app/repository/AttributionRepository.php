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
                a.montant_attribue,
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
}
