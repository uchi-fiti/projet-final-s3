<?php

namespace app\repository;

use Flight;
use PDO;

class BesoinRepository {

    public function getAllBesoins() {
        $db = Flight::db();
        $stmt = $db->prepare("
            SELECT b.*, v.nom AS ville_nom, t.nom AS type_nom,
                   (b.quantite * b.prix_unitaire) AS montant_total,
                   COALESCE(SUM(a.quantite_attribuee), 0) AS total_attribue_qty
            FROM besoins b
            LEFT JOIN villes v ON b.ville_id = v.id
            LEFT JOIN types_besoins t ON b.type_id = t.id
            LEFT JOIN attributions a ON a.besoin_id = b.id
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
            FROM besoins b
            LEFT JOIN villes v ON b.ville_id = v.id
            LEFT JOIN types_besoins t ON b.type_id = t.id
            WHERE b.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createBesoin($data) {
        $db = Flight::db();
        $stmt = $db->prepare("
            INSERT INTO besoins (ville_id, type_id, description, prix_unitaire, quantite, quantite_restante)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $data['ville_id'],
            $data['type_id'],
            $data['description'],
            $data['prix_unitaire'],
            $data['quantite'],
            $data['quantite']
        ]);
        return $db->lastInsertId();
    }

    public function updateBesoin($id, $data) {
        $db = Flight::db();
        $stmt = $db->prepare("
            UPDATE besoins 
            SET ville_id = ?, type_id = ?, description = ?, prix_unitaire = ?, quantite = ?
            WHERE id = ?
        ");
        return $stmt->execute([
            $data['ville_id'],
            $data['type_id'],
            $data['description'],
            $data['prix_unitaire'],
            $data['quantite'],
            $id
        ]);
    }

    public function deleteBesoin($id) {
        $db = Flight::db();
        $stmt = $db->prepare("DELETE FROM besoins WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
