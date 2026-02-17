<?php

namespace app\repository;

use Flight;
use PDO;

class BesoinRepository {

    public function getAllBesoins() {
        $db = Flight::db();
        $stmt = $db->prepare("
            SELECT b.*, v.nom AS ville_nom, t.nom AS type_nom,
                   COALESCE(SUM(a.quantite_attribuee), 0) AS total_attribue_qty,
                   COALESCE(SUM(a.montant_attribue), 0) AS total_attribue_montant
            FROM bngrc_besoins b
            LEFT JOIN bngrc_villes v ON b.ville_id = v.id
            LEFT JOIN bngrc_types_besoins t ON b.type_id = t.id
            LEFT JOIN bngrc_attributions a ON a.besoin_id = b.id
            GROUP BY b.id
            ORDER BY b.date_saisie DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getBesoinById($id) {
        $db = Flight::db();
        $stmt = $db->prepare("
            SELECT b.*, v.nom AS ville_nom, t.nom AS type_nom
            FROM bngrc_besoins b
            LEFT JOIN bngrc_villes v ON b.ville_id = v.id
            LEFT JOIN bngrc_types_besoins t ON b.type_id = t.id
            WHERE b.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createBesoin($data) {
        $db = Flight::db();
        $isArgent = !empty($data['is_argent']);

        if ($isArgent) {
            $stmt = $db->prepare("
                INSERT INTO bngrc_besoins (ville_id, type_id, description, montant, montant_restant)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $data['ville_id'],
                $data['type_id'],
                $data['description'],
                $data['montant'],
                $data['montant'],
            ]);
        } else {
            $stmt = $db->prepare("
                INSERT INTO bngrc_besoins (ville_id, type_id, description, prix_unitaire, quantite, quantite_restante)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $data['ville_id'],
                $data['type_id'],
                $data['description'],
                $data['prix_unitaire'],
                $data['quantite'],
                $data['quantite'],
            ]);
        }
        return $db->lastInsertId();
    }

    public function updateBesoin($id, $data) {
        $db = Flight::db();
        $isArgent = !empty($data['is_argent']);

        if ($isArgent) {
            $stmt = $db->prepare("
                UPDATE bngrc_besoins 
                SET ville_id = ?, type_id = ?, description = ?, montant = ?, montant_restant = ?,
                    prix_unitaire = NULL, quantite = NULL, quantite_restante = NULL
                WHERE id = ?
            ");
            return $stmt->execute([
                $data['ville_id'],
                $data['type_id'],
                $data['description'],
                $data['montant'],
                $data['montant'],
                $id
            ]);
        } else {
            $stmt = $db->prepare("
                UPDATE bngrc_besoins 
                SET ville_id = ?, type_id = ?, description = ?, prix_unitaire = ?, quantite = ?, quantite_restante = ?,
                    montant = NULL, montant_restant = NULL
                WHERE id = ?
            ");
            return $stmt->execute([
                $data['ville_id'],
                $data['type_id'],
                $data['description'],
                $data['prix_unitaire'],
                $data['quantite'],
                $data['quantite'],
                $id
            ]);
        }
    }

    public function deleteBesoin($id) {
        $db = Flight::db();
        $stmt = $db->prepare("DELETE FROM bngrc_besoins WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
