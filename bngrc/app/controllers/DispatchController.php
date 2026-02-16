<?php

namespace app\controllers;
use app\services\DispatchService;
use app\repository\VilleRepository;
use Flight;
use flight\Engine;
use PDO;
use Exception;
class DispatchController {
    public static function simulateDispatch() {
        $pdo = Flight::db();
        $repo = new VilleRepository($pdo);
        $villes = $repo->getVilleDetailedInfo();
        try {
            DispatchService::execute($pdo);
            Flight::render("dashboard", [
                "villes" => $villes,
                "ok" => true
            ]);
        } catch (Exception $e) {
            Flight::render("dashboard", [
                "villes" => $villes,
                "ok" => false,
                "error" => $e->getMessage()
            ]);
        }
    }
}