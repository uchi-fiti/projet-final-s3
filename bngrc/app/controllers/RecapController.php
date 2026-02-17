<?php

namespace app\controllers;

use Flight;
use PDO;

class RecapController
{
    /**
     * Calcule les 3 montants récapitulatifs.
     */
    private static function getRecapData(): array
    {
        $db = Flight::db();

        // Besoins totaux en montant (hors argent)
        $stmt = $db->query("SELECT COALESCE(SUM(prix_unitaire*quantite), 0) AS total FROM bngrc_besoins WHERE type_id != 3");
        $besoinsTotaux = (float) $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Besoins satisfaits en montant (hors argent)
        $stmt = $db->query("SELECT COALESCE(SUM(prix_unitaire*(quantite-    quantite_restante)), 0) AS total FROM bngrc_besoins WHERE type_id != 3");
        $besoinsSatisfaits = (float) $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Montant des besoins restants (hors argent)
        $stmt = $db->query("SELECT COALESCE(SUM(prix_unitaire*quantite_restante), 0) AS total FROM bngrc_besoins WHERE type_id != 3 AND quantite_restante > 0");
        $besoinsRestants = (float) $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        return [
            'besoins_totaux'     => $besoinsTotaux,
            'besoins_satisfaits' => $besoinsSatisfaits,
            'besoins_restants'   => $besoinsRestants,
        ];
    }

    /**
     * Page HTML de récapitulation.
     */
    public static function index()
    {
        $data = self::getRecapData();
        Flight::render('recap', $data);
    }

    /**
     * Endpoint JSON pour l'actualisation AJAX.
     */
    public static function apiJson()
    {
        $data = self::getRecapData();
        Flight::json($data);
    }
}
