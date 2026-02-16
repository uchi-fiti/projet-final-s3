<?php

namespace app\controllers;

use app\repository\VilleRepository;
use Flight;
use PDO;

class DashboardController {

    public static function index() {
        $pdo = Flight::db();
        $repo = new VilleRepository($pdo);
        $villes = $repo->getVilleDetailedInfo();
        $stats = $repo->getDashboardStats();

        // Get flash message from session
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $dispatchOk = $_SESSION['dispatch_ok'] ?? null;
        $dispatchError = $_SESSION['dispatch_error'] ?? null;
        $dispatchMessage = $_SESSION['dispatch_message'] ?? null;
        unset($_SESSION['dispatch_ok'], $_SESSION['dispatch_error'], $_SESSION['dispatch_message']);

        Flight::render("dashboard", [
            "villes" => $villes,
            "stats" => $stats,
            "dispatch_ok" => $dispatchOk,
            "dispatch_error" => $dispatchError,
            "dispatch_message" => $dispatchMessage,
        ]);
    }
}
