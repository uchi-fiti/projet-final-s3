<?php

namespace app\repository;

use Flight;
use PDO;

class RegionRepository {

    public function getAllRegions() {
        $db = Flight::db();
        $stmt = $db->prepare("SELECT id, nom FROM regions ORDER BY nom ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRegionById($id) {
        $db = Flight::db();
        $stmt = $db->prepare("SELECT id, nom FROM regions WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createRegion($nom) {
        $db = Flight::db();
        $stmt = $db->prepare("INSERT INTO regions (nom) VALUES (?)");
        $stmt->execute([$nom]);
        return $db->lastInsertId();
    }

    public function updateRegion($id, $nom) {
        $db = Flight::db();
        $stmt = $db->prepare("UPDATE regions SET nom = ? WHERE id = ?");
        return $stmt->execute([$nom, $id]);
    }

    public function deleteRegion($id) {
        $db = Flight::db();
        $stmt = $db->prepare("DELETE FROM regions WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
