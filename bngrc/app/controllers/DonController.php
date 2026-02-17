<?php

namespace app\controllers;
use Flight;
use flight\Engine;
use app\repository\DonRepository;
use app\repository\TypeRepository;

class DonController {
    
    private $donRepository;
    private $typeRepository;
    
    public function __construct() {
        $this->donRepository = new DonRepository();
        $this->typeRepository = new TypeRepository();
    }
    
    public function showDon(){
        // Get all types for the dropdown
        $types = $this->typeRepository->getAllTypes();
        
        // Get all dons for the table
        $dons = $this->donRepository->getAllDons();
        
        // Get edit don if specified in query
        $editDon = null;
        if (isset($_GET['edit']) && !empty($_GET['edit'])) {
            $editDon = $this->donRepository->getDonById($_GET['edit']);
        }
        
        // Get success/error messages from session
        session_start();
        $message = $_SESSION['message'] ?? null;
        $messageType = $_SESSION['message_type'] ?? null;
        unset($_SESSION['message'], $_SESSION['message_type']);
        
        // Pass data to the view
        Flight::view()->set('types', $types);
        Flight::view()->set('dons', $dons);
        Flight::view()->set('editDon', $editDon);
        Flight::view()->set('message', $message);
        Flight::view()->set('messageType', $messageType);
        Flight::render("crud/dons");
    }
    
    public function createDon() {
        session_start();
        
        // Handle POST request for creating a new don
        $data = [
            'type_id' => $_POST['type_id'] ?? null,
            'description' => $_POST['description'] ?? null,
            'montant' => $_POST['montant'] ?? null,
            'quantite' => $_POST['quantite'] ?? null,
            'date_saisie' => $_POST['date_don'] ?? date('Y-m-d H:i:s')
        ];
        
        // Convert empty strings to null
        $data['montant'] = !empty($data['montant']) ? $data['montant'] : null;
        $data['quantite'] = !empty($data['quantite']) ? $data['quantite'] : null;
        
        // Validate required fields
        if (empty($data['type_id']) || empty($data['description'])) {
            $_SESSION['message'] = 'Type et description sont requis';
            $_SESSION['message_type'] = 'error';
            Flight::redirect(BASE_URL.'/crud/dons');
            return;
        }
        
        $donId = $this->donRepository->createDon($data);
        
        if ($donId) {
            $_SESSION['message'] = 'Don créé avec succès';
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = 'Erreur lors de la création du don';
            $_SESSION['message_type'] = 'error';
        }
        
        Flight::redirect(BASE_URL.'/crud/dons');
    }
    
    public function updateDon($id) {
        session_start();
        
        // Check if don exists
        $existingDon = $this->donRepository->getDonById($id);
        if (!$existingDon) {
            $_SESSION['message'] = 'Don non trouvé';
            $_SESSION['message_type'] = 'error';
            Flight::redirect(BASE_URL.'/crud/dons');
            return;
        }
        
        // Handle POST request for updating a don
        $data = [
            'type_id' => $_POST['type_id'] ?? null,
            'description' => $_POST['description'] ?? null,
            'montant' => $_POST['montant'] ?? null,
            'quantite' => $_POST['quantite'] ?? null,
            'date_saisie' => $_POST['date_don'] ?? date('Y-m-d H:i:s')
        ];
        
        // Convert empty strings to null
        $data['montant'] = !empty($data['montant']) ? $data['montant'] : null;
        $data['quantite'] = !empty($data['quantite']) ? $data['quantite'] : null;
        
        // Validate required fields
        if (empty($data['type_id']) || empty($data['description'])) {
            $_SESSION['message'] = 'Type et description sont requis';
            $_SESSION['message_type'] = 'error';
            Flight::redirect(BASE_URL.'/crud/dons?edit=' . $id);
            return;
        }
        
        $updated = $this->donRepository->updateDon($id, $data);
        
        if ($updated) {
            $_SESSION['message'] = 'Don modifié avec succès';
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = 'Erreur lors de la modification du don';
            $_SESSION['message_type'] = 'error';
        }
        
        Flight::redirect(BASE_URL.'/crud/dons');
    }
    
    public function deleteDon($id) {
        session_start();
        
        // Check if don exists
        $existingDon = $this->donRepository->getDonById($id);
        if (!$existingDon) {
            $_SESSION['message'] = 'Don non trouvé';
            $_SESSION['message_type'] = 'error';
            Flight::redirect(BASE_URL.'/crud/dons');
            return;
        }
        
        // Handle DELETE request for removing a don
        $deleted = $this->donRepository->deleteDon($id);
        
        if ($deleted) {
            $_SESSION['message'] = 'Don supprimé avec succès';
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = 'Erreur lors de la suppression du don';
            $_SESSION['message_type'] = 'error';
        }
        
        Flight::redirect(BASE_URL.'/crud/dons');
    }
    
    public function getDon($id) {
        // Handle GET request for retrieving a single don (for editing)
        $don = $this->donRepository->getDonById($id);
        
        if ($don) {
            Flight::json(['success' => true, 'don' => $don]);
        } else {
            Flight::json(['success' => false, 'message' => 'Don non trouvé'], 404);
        }
    }
}