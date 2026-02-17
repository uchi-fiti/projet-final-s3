<?php

namespace app\controllers;

use app\repository\TypeRepository;
use Flight;

class TypeController {

    public static function index() {
        $repo = new TypeRepository();
        $types = $repo->getAllTypes();

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $message = $_SESSION['message'] ?? null;
        $messageType = $_SESSION['message_type'] ?? null;
        unset($_SESSION['message'], $_SESSION['message_type']);

        Flight::render('types', [
            'types' => $types,
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
            $_SESSION['message'] = 'Le nom du type est requis';
            $_SESSION['message_type'] = 'error';
            Flight::redirect(BASE_URL.'/types');
            return;
        }

        $db = Flight::db();
        $stmt = $db->prepare("INSERT INTO bngrc_types_besoins (nom) VALUES (?)");
        $stmt->execute([$nom]);

        $_SESSION['message'] = 'Type créé avec succès';
        $_SESSION['message_type'] = 'success';
        Flight::redirect(BASE_URL.'/types');
    }

    public static function update($id) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $nom = $_POST['nom'] ?? null;
        if (empty($nom)) {
            $_SESSION['message'] = 'Le nom du type est requis';
            $_SESSION['message_type'] = 'error';
            Flight::redirect(BASE_URL.'/types');
            return;
        }

        $db = Flight::db();
        $stmt = $db->prepare("UPDATE bngrc_types_besoins SET nom = ? WHERE id = ?");
        $stmt->execute([$nom, $id]);

        $_SESSION['message'] = 'Type modifié avec succès';
        $_SESSION['message_type'] = 'success';
        Flight::redirect(BASE_URL.'/types');
    }

    public static function delete($id) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $db = Flight::db();
        $stmt = $db->prepare("DELETE FROM bngrc_types_besoins WHERE id = ?");
        $stmt->execute([$id]);

        $_SESSION['message'] = 'Type supprimé avec succès';
        $_SESSION['message_type'] = 'success';
        Flight::redirect(BASE_URL.'/types');
    }
}
