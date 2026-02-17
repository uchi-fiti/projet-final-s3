<?php

namespace app\controllers;

use app\repository\BesoinRepository;
use app\repository\VilleRepository;
use app\repository\TypeRepository;
use Flight;

class BesoinCrudController {

    public static function index() {
        $besoinRepo = new BesoinRepository();
        $typeRepo = new TypeRepository();
        $pdo = Flight::db();
        $villeRepo = new VilleRepository($pdo);

        $besoins = $besoinRepo->getAllBesoins();
        $types = $typeRepo->getAllTypes();

        $stmt = $pdo->query("SELECT id, nom FROM bngrc_villes ORDER BY nom ASC");
        $villes = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Edit mode
        $editBesoin = null;
        if (isset($_GET['edit']) && !empty($_GET['edit'])) {
            $editBesoin = $besoinRepo->getBesoinById($_GET['edit']);
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $message = $_SESSION['message'] ?? null;
        $messageType = $_SESSION['message_type'] ?? null;
        unset($_SESSION['message'], $_SESSION['message_type']);

        Flight::render('besoins', [
            'besoins' => $besoins,
            'types' => $types,
            'villes' => $villes,
            'editBesoin' => $editBesoin,
            'message' => $message,
            'messageType' => $messageType
        ]);
    }

    public static function create() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $data = [
            'ville_id' => $_POST['ville_id'] ?? null,
            'type_id' => $_POST['type_id'] ?? null,
            'description' => $_POST['description'] ?? null,
            'prix_unitaire' => $_POST['prix_unitaire'] ?? null,
            'quantite' => $_POST['quantite'] ?? null,
        ];

        if (empty($data['ville_id']) || empty($data['type_id']) || empty($data['description']) ||
            empty($data['prix_unitaire']) || empty($data['quantite'])) {
            $_SESSION['message'] = 'Tous les champs sont requis';
            $_SESSION['message_type'] = 'error';
            Flight::redirect(BASE_URL.'/besoins');
            return;
        }

        $repo = new BesoinRepository();
        $repo->createBesoin($data);

        $_SESSION['message'] = 'Besoin créé avec succès';
        $_SESSION['message_type'] = 'success';
        Flight::redirect(BASE_URL.'/besoins');
    }

    public static function update($id) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $data = [
            'ville_id' => $_POST['ville_id'] ?? null,
            'type_id' => $_POST['type_id'] ?? null,
            'description' => $_POST['description'] ?? null,
            'prix_unitaire' => $_POST['prix_unitaire'] ?? null,
            'quantite' => $_POST['quantite'] ?? null,
        ];

        if (empty($data['ville_id']) || empty($data['type_id']) || empty($data['description']) ||
            empty($data['prix_unitaire']) || empty($data['quantite'])) {
            $_SESSION['message'] = 'Tous les champs sont requis';
            $_SESSION['message_type'] = 'error';
            Flight::redirect(BASE_URL.'/besoins');
            return;
        }

        $repo = new BesoinRepository();
        $repo->updateBesoin($id, $data);

        $_SESSION['message'] = 'Besoin modifié avec succès';
        $_SESSION['message_type'] = 'success';
        Flight::redirect(BASE_URL.'/besoins');
    }

    public static function delete($id) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $repo = new BesoinRepository();
        $repo->deleteBesoin($id);

        $_SESSION['message'] = 'Besoin supprimé avec succès';
        $_SESSION['message_type'] = 'success';
        Flight::redirect(BASE_URL.'/besoins');
    }
}
