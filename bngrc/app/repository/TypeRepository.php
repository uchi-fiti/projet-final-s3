<?php

namespace app\repository;
use Flight;

class TypeRepository {
    
    /**
     * Get all types from types_besoins table
     * @return array
     */
    public function getAllTypes() {
        $db = Flight::db();
        $stmt = $db->prepare("SELECT id, nom FROM bngrc_types_besoins ORDER BY nom");
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    /**
     * Get a specific type by ID
     * @param int $id
     * @return array|false
     */
    public function getTypeById($id) {
        $db = Flight::db();
        $stmt = $db->prepare("SELECT id, nom FROM bngrc_types_besoins WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
}