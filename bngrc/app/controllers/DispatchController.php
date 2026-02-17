<?php

namespace app\controllers;

use app\services\DispatchService;
use Flight;
use PDO;
use Exception;

class DispatchController {

    /**
     * FIFO Dispatch - Original behavior
     */
    public static function simulateDispatch() {
        $pdo = Flight::db();

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        try {
            DispatchService::execute($pdo);
            $_SESSION['dispatch_ok'] = true;
            $_SESSION['dispatch_mode'] = 'FIFO';
        } catch (Exception $e) {
            $_SESSION['dispatch_ok'] = false;
            $_SESSION['dispatch_error'] = $e->getMessage();
        }

        Flight::redirect(BASE_URL.'/attributions');    
    }

    /**
     * Proportional Dispatch - Uses Largest Remainder Method
     */
    public static function simulateProportional() {
        $pdo = Flight::db();

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        try {
            DispatchService::executeProportional($pdo);
            $_SESSION['dispatch_ok'] = true;
            $_SESSION['dispatch_mode'] = 'Proportionnel';
        } catch (Exception $e) {
            $_SESSION['dispatch_ok'] = false;
            $_SESSION['dispatch_error'] = $e->getMessage();
        }

        Flight::redirect(BASE_URL.'/attributions');    
    }
}