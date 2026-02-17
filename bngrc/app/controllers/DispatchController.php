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

        Flight::redirect(BASE_URL.'/dashboard');    
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

    /**
     * Reset : supprime les attributions et remet les quantités/montants restants à leur valeur initiale.
     */
    public static function resetData() {
        $pdo = Flight::db();

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        try {
            $pdo->beginTransaction();

            $pdo->exec('DELETE FROM bngrc_attributions');
            $pdo->exec('UPDATE bngrc_besoins SET quantite_restante = quantite WHERE quantite IS NOT NULL');
            $pdo->exec('UPDATE bngrc_besoins SET montant_restant = montant WHERE montant IS NOT NULL');
            $pdo->exec('UPDATE bngrc_dons SET quantite_restante = quantite WHERE quantite IS NOT NULL');
            $pdo->exec('UPDATE bngrc_dons SET montant_restant = montant WHERE montant IS NOT NULL');

            $pdo->commit();

            $_SESSION['dispatch_ok'] = true;
            $_SESSION['dispatch_mode'] = 'Reset';
        } catch (Exception $e) {
            $pdo->rollBack();
            $_SESSION['dispatch_ok'] = false;
            $_SESSION['dispatch_error'] = $e->getMessage();
        }

        Flight::redirect(BASE_URL.'/attributions');
    }
}