<?php

namespace app\controllers;

use app\services\DispatchService;
use Flight;
use PDO;
use Exception;

class DispatchController {

    public static function simulateDispatch() {
        $pdo = Flight::db();

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        try {
            DispatchService::execute($pdo);
            $_SESSION['dispatch_ok'] = true;
        } catch (Exception $e) {
            $_SESSION['dispatch_ok'] = false;
            $_SESSION['dispatch_error'] = $e->getMessage();
        }

        Flight::redirect(BASE_URL.'/attributions');    
    }

    public static function dispatchSmallestFirst() {
        $pdo = Flight::db();

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        try {
            DispatchService::executeSmallestFirst($pdo);
            $_SESSION['dispatch_ok'] = true;
        } catch (Exception $e) {
            $_SESSION['dispatch_ok'] = false;
            $_SESSION['dispatch_error'] = $e->getMessage();
        }

        Flight::redirect(BASE_URL.'/attributions');    
    }
}