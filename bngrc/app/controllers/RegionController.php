<?php

namespace app\controllers;

use app\repository\RegionRepository;
use Flight;

class RegionController {

    public static function index() {
        $repo = new RegionRepository();
        $regions = $repo->getAllRegions();

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $message = $_SESSION['message'] ?? null;
        $messageType = $_SESSION['message_type'] ?? null;
        unset($_SESSION['message'], $_SESSION['message_type']);

        Flight::render('regions', [
            'regions' => $regions,
            'message' => $message,
            'messageType' => $messageType
        ]);
    }

    public static function create() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $nom = $_POST['nom'] ?? null;
        if (empty($nom)) {
            $_SESSION['message'] = 'Le nom de la région est requis';
            $_SESSION['message_type'] = 'error';
            Flight::redirect('/regions');
            return;
        }

        $repo = new RegionRepository();
        $repo->createRegion($nom);
        $_SESSION['message'] = 'Région créée avec succès';
        $_SESSION['message_type'] = 'success';
        Flight::redirect('/regions');
    }

    public static function update($id) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $nom = $_POST['nom'] ?? null;
        if (empty($nom)) {
            $_SESSION['message'] = 'Le nom de la région est requis';
            $_SESSION['message_type'] = 'error';
            Flight::redirect('/regions');
            return;
        }

        $repo = new RegionRepository();
        $repo->updateRegion($id, $nom);
        $_SESSION['message'] = 'Région modifiée avec succès';
        $_SESSION['message_type'] = 'success';
        Flight::redirect('/regions');
    }

    public static function delete($id) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $repo = new RegionRepository();
        $repo->deleteRegion($id);
        $_SESSION['message'] = 'Région supprimée avec succès';
        $_SESSION['message_type'] = 'success';
        Flight::redirect('/regions');
    }
}
