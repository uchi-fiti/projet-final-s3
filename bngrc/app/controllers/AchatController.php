<?php

namespace app\controllers;

use app\services\AchatService;
use app\repository\BesoinRepository;
use Flight;
use PDO;

class AchatController
{
    /**
     * Affiche la liste des besoins restants (quantite_restante > 0)
     * avec un bouton "Acheter" par ligne.
     */
    public static function besoinsRestants()
    {
        $db = Flight::db();

        // Filtre par ville (optionnel)
        $villeId = isset($_GET['ville_id']) && $_GET['ville_id'] !== '' ? (int) $_GET['ville_id'] : null;

        // Liste des villes pour le dropdown
        $stmtVilles = $db->query("SELECT id, nom FROM bngrc_villes ORDER BY nom");
        $villes = $stmtVilles->fetchAll(PDO::FETCH_ASSOC);

        // Besoins restants avec filtre optionnel
        $sql = "
            SELECT b.id, b.description, b.prix_unitaire, b.quantite, b.quantite_restante,
                   v.nom AS ville_nom, t.nom AS type_nom
            FROM bngrc_besoins b
            JOIN bngrc_villes v ON b.ville_id = v.id
            JOIN bngrc_types_besoins t ON b.type_id = t.id
            WHERE b.quantite_restante > 0 
            AND b.type_id != 3
        ";
        $params = [];
        if ($villeId !== null) {
            $sql .= " AND b.ville_id = ?";
            $params[] = $villeId;
        }
        $sql .= " ORDER BY v.nom, t.nom";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $besoins = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fonds argent disponibles
        $stmtFonds = $db->prepare("
            SELECT COALESCE(SUM(d.montant_restant), 0) AS fonds
            FROM bngrc_dons d
            JOIN bngrc_types_besoins t ON d.type_id = t.id
            WHERE t.nom = 'Argent' AND d.montant_restant > 0
        ");
        $stmtFonds->execute();
        $fondsDisponibles = (float) $stmtFonds->fetch(PDO::FETCH_ASSOC)['fonds'];

        // Flash messages
        if (session_status() === PHP_SESSION_NONE) session_start();
        $message     = $_SESSION['achat_message'] ?? null;
        $messageType = $_SESSION['achat_message_type'] ?? 'info';
        unset($_SESSION['achat_message'], $_SESSION['achat_message_type']);

        Flight::render('besoins_restants', [
            'besoins'           => $besoins,
            'fonds_disponibles' => $fondsDisponibles,
            'villes'            => $villes,
            'ville_id'          => $villeId,
            'message'           => $message,
            'messageType'       => $messageType,
        ]);
    }

    /**
     * Affiche le formulaire d'achat pour un besoin donné.
     */
    public static function showAchatForm($id)
    {
        $repo   = new BesoinRepository();
        $besoin = $repo->getBesoinById($id);

        if (!$besoin || $besoin['quantite_restante'] <= 0) {
            if (session_status() === PHP_SESSION_NONE) session_start();
            $_SESSION['achat_message']      = 'Besoin introuvable ou déjà entièrement couvert.';
            $_SESSION['achat_message_type']  = 'danger';
            Flight::redirect(BASE_URL.'/besoins-restants');
            return;
        }

        // Fonds argent disponibles
        $db = Flight::db();
        $stmtFonds = $db->prepare("
            SELECT COALESCE(SUM(d.montant_restant), 0) AS fonds
            FROM bngrc_dons d
            JOIN bngrc_types_besoins t ON d.type_id = t.id
            WHERE t.nom = 'Argent' AND d.montant_restant > 0
        ");
        $stmtFonds->execute();
        $fondsDisponibles = (float) $stmtFonds->fetch(PDO::FETCH_ASSOC)['fonds'];

        // Check for flash messages in session
        if (session_status() === PHP_SESSION_NONE) session_start();
        $message     = $_SESSION['achat_message'] ?? null;
        $messageType = $_SESSION['achat_message_type'] ?? 'info';
        unset($_SESSION['achat_message'], $_SESSION['achat_message_type']);

        Flight::render('achat', [
            'besoin'            => $besoin,
            'fonds_disponibles' => $fondsDisponibles,
            'message'           => $message,
            'messageType'       => $messageType,
        ]);
    }

    /**
     * POST – Simuler un achat (ne modifie rien en base).
     * Succès → affiche la page de résultat.
     * Échec  → redirige vers le formulaire avec un message d'erreur.
     */
    public static function simulate()
    {
        $besoin_id     = (int) ($_POST['besoin_id'] ?? 0);
        $quantite      = (int) ($_POST['quantite'] ?? 0);
        $frais_percent = (float) ($_POST['frais_pourcentage'] ?? 0);

        $pdo    = Flight::db();
        $result = AchatService::simulateAchat($pdo, $besoin_id, $quantite, $frais_percent);

        if ($result['success']) {
            // Succès : afficher la page de résultat directement
            Flight::render('achat_resultat', [
                'simulation' => $result,
            ]);
        } else {
            // Échec : rediriger vers le formulaire avec message d'erreur
            if (session_status() === PHP_SESSION_NONE) session_start();
            $_SESSION['achat_message']      = $result['message'];
            $_SESSION['achat_message_type'] = 'danger';
            Flight::redirect(BASE_URL."/achat/$besoin_id");
        }
    }

    /**
     * POST – Valider et exécuter un achat.
     */
    public static function validate()
    {
        $besoin_id     = (int) ($_POST['besoin_id'] ?? 0);
        $quantite      = (int) ($_POST['quantite'] ?? 0);
        $frais_percent = (float) ($_POST['frais_pourcentage'] ?? 0);

        $pdo    = Flight::db();
        $result = AchatService::validerAchat($pdo, $besoin_id, $quantite, $frais_percent);

        if (session_status() === PHP_SESSION_NONE) session_start();

        if ($result['success']) {
            $_SESSION['dispatch_ok'] = true;
            $_SESSION['dispatch_message'] = $result['message'];
            Flight::redirect(BASE_URL.'/dashboard');
        } else {
            $_SESSION['achat_message']      = $result['message'];
            $_SESSION['achat_message_type'] = 'danger';
            Flight::redirect(BASE_URL."/achat/$besoin_id");
        }
    }
}
