<?php

namespace app\controllers;

use app\repository\VilleRepository;
use app\repository\RegionRepository;
use Flight;

class VilleController {

    public static function index() {
        $pdo = Flight::db();
        $villeRepo = new VilleRepository($pdo);
        $regionRepo = new RegionRepository();

        // Get villes with their region name
        $stmt = $pdo->query("
            SELECT v.id, v.nom, v.region_id, r.nom AS region_nom
            FROM bngrc_villes v
                LEFT JOIN bngrc_regions r ON v.region_id = r.id
            ORDER BY v.nom ASC
        ");
        $villes = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $regions = $regionRepo->getAllRegions();

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $message = $_SESSION['message'] ?? null;
        $messageType = $_SESSION['message_type'] ?? null;
        unset($_SESSION['message'], $_SESSION['message_type']);

        Flight::render('villes', [
            'villes' => $villes,
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
        $region_id = $_POST['region_id'] ?? null;

        if (empty($nom) || empty($region_id)) {
            $_SESSION['message'] = 'Nom et région sont requis';
            $_SESSION['message_type'] = 'error';
            Flight::redirect('/villes');
            return;
        }

        $db = Flight::db();
        $stmt = $db->prepare("INSERT INTO bngrc_villes (nom, region_id) VALUES (?, ?)");
        $stmt->execute([$nom, $region_id]);

        $_SESSION['message'] = 'Ville créée avec succès';
        $_SESSION['message_type'] = 'success';
        Flight::redirect('/villes');
    }

    public static function update($id) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $nom = $_POST['nom'] ?? null;
        $region_id = $_POST['region_id'] ?? null;

        if (empty($nom) || empty($region_id)) {
            $_SESSION['message'] = 'Nom et région sont requis';
            $_SESSION['message_type'] = 'error';
            Flight::redirect('/villes');
            return;
        }

        $db = Flight::db();
        $stmt = $db->prepare("UPDATE bngrc_villes SET nom = ?, region_id = ? WHERE id = ?");
        $stmt->execute([$nom, $region_id, $id]);

        $_SESSION['message'] = 'Ville modifiée avec succès';
        $_SESSION['message_type'] = 'success';
        Flight::redirect('/villes');
    }

    public static function delete($id) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $db = Flight::db();
        $stmt = $db->prepare("DELETE FROM bngrc_villes WHERE id = ?");
        $stmt->execute([$id]);

        $_SESSION['message'] = 'Ville supprimée avec succès';
        $_SESSION['message_type'] = 'success';
        Flight::redirect('/villes');
    }
}
