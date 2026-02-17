<?php

namespace app\controllers;

use flight\Engine;

class BesoinController {

    protected Engine $app;

    public function __construct(Engine $app) {
        $this->app = $app;
    }

    /** Helper: does a column exist in the current database? */
    protected function columnExists(string $table, string $column): bool {
        $db = $this->app->db();
        $row = $db->fetchRow(
            "SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?",
            [ $table, $column ]
        );
        return (bool) $row;
    }

    /**
     * Afficher la liste de tous les besoins (compatible avec plusieurs schémas)
     */
    public function index() {
        $db = $this->app->db();

        // Query agnostique : utilise COALESCE pour prendre les bonnes colonnes
        $sql = "SELECT b.*, v.nom AS ville_nom,
                       COALESCE(b.designation, b.description) AS designation,
                       COALESCE(b.type_besoin, t.nom) AS type_besoin,
                       COALESCE(b.created_at, b.date_saisie) AS created_at
                FROM besoins b
                LEFT JOIN villes v ON b.ville_id = v.id
                LEFT JOIN types_besoins t ON b.type_id = t.id
                ORDER BY COALESCE(b.created_at, b.date_saisie) DESC";

        try {
            $besoins = $db->fetchAll($sql);

            $this->app->render('besoins/index', [
                'besoins' => $besoins,
                'message'  => $this->app->request()->query->message ?? null
            ]);

        } catch (\Throwable $e) {
            // Écrire la trace pour diagnostic local
            $logFile = PROJECT_ROOT . '/debug_besoins.log';
            $msg = date('c') . " - Exception in BesoinController::index(): \n" . $e->__toString() . "\n\n";
            @file_put_contents($logFile, $msg, FILE_APPEND);

            // Renvoyer une erreur 500 lisible
            $this->app->halt(500, 'Erreur interne (voir debug_besoins.log)');
        }
    }

    /**
     * Afficher le formulaire de création
     */
    public function create() {
        $db = $this->app->db();
        $villes = $db->fetchAll("SELECT * FROM villes ORDER BY nom");

        // Fournir la liste des types si elle existe (compatibilité)
        $types = [];
        if ($this->columnExists('types_besoins', 'nom')) {
            $types = $db->fetchAll("SELECT * FROM types_besoins ORDER BY nom");
        }

        $this->app->render('besoins/create', [
            'villes' => $villes,
            'types'  => $types
        ]);
    }

    /**
     * Enregistrer un nouveau besoin (s'adapte au schéma présent)
     */
    public function store() {
        $db = $this->app->db();
        $request = $this->app->request();

        $ville_id = $request->data->ville_id ?? null;
        $type_besoin = $request->data->type_besoin ?? null; // may be string (nature) or an id mapping
        $designation = $request->data->designation ?? null;
        $quantite = $request->data->quantite ?? null;
        $prix_unitaire = $request->data->prix_unitaire ?? null;

        if (!$ville_id || !$type_besoin || !$designation || !$quantite || !$prix_unitaire) {
            $this->app->redirect('/besoins?message=error_missing_fields');
            return;
        }

        // If DB expects `type_besoin` string column -> use it
        if ($this->columnExists('besoins', 'type_besoin')) {
            $sql = "INSERT INTO besoins (ville_id, type_besoin, designation, quantite, prix_unitaire, created_at)
                    VALUES (?, ?, ?, ?, ?, NOW())";

            $db->runQuery($sql, [ $ville_id, $type_besoin, $designation, $quantite, $prix_unitaire ]);

        } else {
            // Fallback: DB uses type_id + description columns (older schema)
            // Try to resolve type_id from types_besoins table (if exists)
            $type_id = null;
            if ($this->columnExists('types_besoins', 'nom')) {
                $row = $db->fetchRow("SELECT id FROM types_besoins WHERE nom = ? LIMIT 1", [ $type_besoin ]);
                $type_id = $row['id'] ?? null;
            }

            $sql = "INSERT INTO besoins (ville_id, type_id, description, quantite, prix_unitaire, date_saisie)
                    VALUES (?, ?, ?, ?, ?, NOW())";
            $db->runQuery($sql, [ $ville_id, $type_id, $designation, $quantite, $prix_unitaire ]);
        }

        $this->app->redirect('/besoins?message=success_created');
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit($id) {
        $db = $this->app->db();
        $besoin = $db->fetchRow("SELECT * FROM besoins WHERE id = ?", [$id]);

        if (!$besoin) {
            $this->app->redirect('/besoins?message=error_not_found');
            return;
        }

        // Harmoniser les champs pour la vue
        if (empty($besoin['designation']) && !empty($besoin['description'])) {
            $besoin['designation'] = $besoin['description'];
        }
        if (empty($besoin['type_besoin']) && !empty($besoin['type_id']) && $this->columnExists('types_besoins', 'nom')) {
            $row = $db->fetchRow("SELECT nom FROM types_besoins WHERE id = ?", [ $besoin['type_id'] ]);
            $besoin['type_besoin'] = $row['nom'] ?? null;
        }
        if (empty($besoin['created_at']) && !empty($besoin['date_saisie'])) {
            $besoin['created_at'] = $besoin['date_saisie'];
        }

        $villes = $db->fetchAll("SELECT * FROM villes ORDER BY nom");

        $this->app->render('besoins/edit', [
            'besoin' => $besoin,
            'villes' => $villes
        ]);
    }

    /**
     * Mettre à jour un besoin (compatible avec deux schémas)
     */
    public function update($id) {
        $db = $this->app->db();
        $request = $this->app->request();

        $ville_id = $request->data->ville_id ?? null;
        $type_besoin = $request->data->type_besoin ?? null;
        $designation = $request->data->designation ?? null;
        $quantite = $request->data->quantite ?? null;
        $prix_unitaire = $request->data->prix_unitaire ?? null;

        if (!$ville_id || !$type_besoin || !$designation || !$quantite || !$prix_unitaire) {
            $this->app->redirect("/besoins/edit/$id?message=error_missing_fields");
            return;
        }

        if ($this->columnExists('besoins', 'type_besoin')) {
            $sql = "UPDATE besoins SET ville_id = ?, type_besoin = ?, designation = ?, quantite = ?, prix_unitaire = ?, updated_at = NOW() WHERE id = ?";
            $db->runQuery($sql, [ $ville_id, $type_besoin, $designation, $quantite, $prix_unitaire, $id ]);
        } else {
            // Resolve type_id if possible
            $type_id = null;
            if ($this->columnExists('types_besoins', 'nom')) {
                $row = $db->fetchRow("SELECT id FROM types_besoins WHERE nom = ? LIMIT 1", [ $type_besoin ]);
                $type_id = $row['id'] ?? null;
            }
            $sql = "UPDATE besoins SET ville_id = ?, type_id = ?, description = ?, quantite = ?, prix_unitaire = ?, date_saisie = NOW() WHERE id = ?";
            $db->runQuery($sql, [ $ville_id, $type_id, $designation, $quantite, $prix_unitaire, $id ]);
        }

        $this->app->redirect('/besoins?message=success_updated');
    }

    /**
     * Supprimer un besoin
     */
    public function delete($id) {
        $db = $this->app->db();

        $besoin = $db->fetchRow("SELECT * FROM besoins WHERE id = ?", [$id]);
        if (!$besoin) {
            $this->app->redirect('/besoins?message=error_not_found');
            return;
        }

        $db->runQuery("DELETE FROM besoins WHERE id = ?", [$id]);
        $this->app->redirect('/besoins?message=success_deleted');
    }
}
