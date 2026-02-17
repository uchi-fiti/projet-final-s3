<?php

namespace app\controllers;

use flight\Engine;

class BesoinController {
    
    protected Engine $app;

    public function __construct(Engine $app) {
        $this->app = $app;
    }

    /**
     * Afficher la liste de tous les besoins
     */
    public function index() {
        // Récupérer tous les besoins depuis la base de données
        $db = $this->app->db();
        $besoins = $db->fetchAll("
            SELECT b.*, v.nom as ville_nom 
            FROM besoins b
            LEFT JOIN villes v ON b.ville_id = v.id
            ORDER BY b.created_at DESC
        ");
        
        $this->app->render('besoins/index', [
            'besoins' => $besoins,
            'message' => $this->app->request()->query->message ?? null
        ]);
    }

    /**
     * Afficher le formulaire de création
     */
    public function create() {
        // Récupérer la liste des villes pour le select
        $db = $this->app->db();
        $villes = $db->fetchAll("SELECT * FROM villes ORDER BY nom");
        
        $this->app->render('besoins/create', [
            'villes' => $villes
        ]);
    }

    /**
     * Enregistrer un nouveau besoin
     */
    public function store() {
        $db = $this->app->db();
        $request = $this->app->request();
        
        // Validation des données
        $ville_id = $request->data->ville_id ?? null;
        $type_besoin = $request->data->type_besoin ?? null; // nature, materiau, argent
        $designation = $request->data->designation ?? null;
        $quantite = $request->data->quantite ?? null;
        $prix_unitaire = $request->data->prix_unitaire ?? null;
        
        // Vérifications
        if (!$ville_id || !$type_besoin || !$designation || !$quantite || !$prix_unitaire) {
            $this->app->redirect('/besoins?message=error_missing_fields');
            return;
        }

        // Insertion dans la base de données
        $sql = "INSERT INTO besoins (ville_id, type_besoin, designation, quantite, prix_unitaire, created_at) 
                VALUES (?, ?, ?, ?, ?, NOW())";
        
        $db->runQuery($sql, [
            $ville_id,
            $type_besoin,
            $designation,
            $quantite,
            $prix_unitaire
        ]);
        
        $this->app->redirect('/besoins?message=success_created');
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit($id) {
        $db = $this->app->db();
        
        // Récupérer le besoin à modifier
        $besoin = $db->fetchRow("SELECT * FROM besoins WHERE id = ?", [$id]);
        
        if (!$besoin) {
            $this->app->redirect('/besoins?message=error_not_found');
            return;
        }
        
        // Récupérer la liste des villes
        $villes = $db->fetchAll("SELECT * FROM villes ORDER BY nom");
        
        $this->app->render('besoins/edit', [
            'besoin' => $besoin,
            'villes' => $villes
        ]);
    }

    /**
     * Mettre à jour un besoin
     */
    public function update($id) {
        $db = $this->app->db();
        $request = $this->app->request();
        
        // Récupération des données
        $ville_id = $request->data->ville_id ?? null;
        $type_besoin = $request->data->type_besoin ?? null;
        $designation = $request->data->designation ?? null;
        $quantite = $request->data->quantite ?? null;
        $prix_unitaire = $request->data->prix_unitaire ?? null;
        
        // Vérifications
        if (!$ville_id || !$type_besoin || !$designation || !$quantite || !$prix_unitaire) {
            $this->app->redirect("/besoins/edit/$id?message=error_missing_fields");
            return;
        }
        
        // Mise à jour
        $sql = "UPDATE besoins 
                SET ville_id = ?, type_besoin = ?, designation = ?, quantite = ?, prix_unitaire = ?, updated_at = NOW()
                WHERE id = ?";
        
        $db->runQuery($sql, [
            $ville_id,
            $type_besoin,
            $designation,
            $quantite,
            $prix_unitaire,
            $id
        ]);
        
        $this->app->redirect('/besoins?message=success_updated');
    }

    /**
     * Supprimer un besoin
     */
    public function delete($id) {
        $db = $this->app->db();
        
        // Vérifier si le besoin existe
        $besoin = $db->fetchRow("SELECT * FROM besoins WHERE id = ?", [$id]);
        
        if (!$besoin) {
            $this->app->redirect('/besoins?message=error_not_found');
            return;
        }
        
        // Suppression
        $db->runQuery("DELETE FROM besoins WHERE id = ?", [$id]);
        
        $this->app->redirect('/besoins?message=success_deleted');
    }
}
