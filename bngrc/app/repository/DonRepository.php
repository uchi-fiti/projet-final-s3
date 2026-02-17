<?php

namespace app\repository;
use Flight;

class DonRepository {
    
    /**
     * Insert a new don into the database
     * @param array $data
     * @return int|false The inserted ID or false on failure
     */
    public function createDon($data) {
        $db = Flight::db();
        
        $stmt = $db->prepare("
            INSERT INTO bngrc_dons (type_id, description, montant, quantite, date_saisie, quantite_restante)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        
        $result = $stmt->execute([
            $data['type_id'],
            $data['description'],
            $data['montant'],
            $data['quantite'],
            $data['date_saisie'],
            $data['quantite']
        ]);
        
        if ($result) {
            return $db->lastInsertId();
        }
        
        return false;
    }
    
    /**
     * Get all dons with type information
     * @return array
     */
    public function getAllDons() {
        $db = Flight::db();
        
        $stmt = $db->prepare("
            SELECT d.*, t.nom as type_nom 
            FROM bngrc_dons d 
            LEFT JOIN bngrc_types_besoins t ON d.type_id = t.id 
            ORDER BY d.date_saisie DESC
        ");
        
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    /**
     * Get a specific don by ID
     * @param int $id
     * @return array|false
     */
    public function getDonById($id) {
        $db = Flight::db();
        
        $stmt = $db->prepare("
            SELECT d.*, t.nom as type_nom 
            FROM bngrc_dons d 
            LEFT JOIN bngrc_types_besoins t ON d.type_id = t.id 
            WHERE d.id = ?
        ");
        
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
    
    /**
     * Update an existing don
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updateDon($id, $data) {
        $db = Flight::db();
        
        $stmt = $db->prepare("
            UPDATE bngrc_dons 
            SET type_id = ?, description = ?, montant = ?, quantite = ?, date_saisie = ?
            WHERE id = ?
        ");
        
        return $stmt->execute([
            $data['type_id'],
            $data['description'],
            $data['montant'],
            $data['quantite'],
            $data['date_saisie'],
            $id
        ]);
    }
    
    /**
     * Delete a don by ID
     * @param int $id
     * @return bool
     */
    public function deleteDon($id) {
        $db = Flight::db();
        
        $stmt = $db->prepare("DELETE FROM bngrc_dons WHERE id = ?");
        return $stmt->execute([$id]);
    }
}