<?php

namespace app\controllers;

use flight\Engine;

/**
 * Controller pour la page de simulation / validation des attributions
 * - /attributions           GET  -> affiche la page
 * - /attributions/simulate  POST -> retourne la simulation (JSON)
 * - /attributions/validate  POST -> exécute le dispatch réel (insert dans attributions)
 */
class AttributionController {
    protected Engine $app;

    public function __construct(Engine $app) {
        $this->app = $app;
    }

    public function index() {
        // page simple — la logique de simulation est faite en AJAX
        $this->app->render('attributions/index', []);
    }

    /**
     * POST /attributions/simulate
     * body: { fee_pct: number }
     * retourne JSON avec la liste des attributions simulées (sans écrire en base)
     */
    public function simulate() {
        $req = $this->app->request();
        $feePct = floatval($req->data->fee_pct ?? 0);
        $result = $this->computeAllocations($feePct);

        $this->app->response()->header('Content-Type', 'application/json');
        echo json_encode([ 'ok' => true, 'fee_pct' => $feePct, 'result' => $result ]);
    }

    /**
     * POST /attributions/validate
     * Ré-exécute l'algorithme côté serveur et persiste les attributions dans la table `attributions`.
     */
    public function validate() {
        $req = $this->app->request();
        $feePct = floatval($req->data->fee_pct ?? 0);
        $allocations = $this->computeAllocations($feePct);
        $db = $this->app->db();

        try {
            $inserted = 0;
            $db->transaction(function($db) use ($allocations, &$inserted) {
                foreach ($allocations['allocations'] as $a) {
                    // protection: n'insérer que les quantités > 0
                    if (empty($a['quantite_attribuee']) || floatval($a['quantite_attribuee']) <= 0) {
                        continue;
                    }
                    $db->runQuery(
                        "INSERT INTO attributions (besoin_id, don_id, quantite_attribuee, date_attribution) VALUES (?, ?, ?, NOW())",
                        [ $a['besoin_id'], $a['don_id'], $a['quantite_attribuee'] ]
                    );
                    $inserted++;
                }
            });

            $this->app->response()->header('Content-Type', 'application/json');
            echo json_encode([ 'ok' => true, 'inserted' => $inserted, 'message' => "Attributions validées (\"$inserted\" enregistrements)." ]);

        } catch (\Throwable $e) {
            $this->app->response()->status(500);
            $this->app->response()->header('Content-Type', 'application/json');
            echo json_encode([ 'ok' => false, 'error' => $e->getMessage() ]);
        }
    }

    /**
     * Algorithme d'attribution (utilisé par simulate() et validate()).
     * Retourne tableau: [ allocations: [], totals: [] ]
     * - Règles principales implémentées :
     *   - On parcourt les DONS par date_saisie ASC (ordre de saisie)
     *   - On attribue aux BESOINS par date_saisie/created_at ASC (FIFO)
     *   - Dons de type 'argent' peuvent acheter besoins 'nature'/'materiau' (frais applicables)
     *   - Dons non-monétaires n'affectent que besoins du même `type` (matching par désignation prioritaire)
     *   - Les attributions sont partielles si quantité insuffisante
     */
    protected function computeAllocations(float $feePct = 0.0): array {
        $db = $this->app->db();

        // Récupérer besoins avec quantite restante (prend en compte attributions existantes)
        $sqlB = "SELECT b.*, v.nom AS ville_nom,
                        COALESCE(b.designation, b.description) AS designation,
                        COALESCE(b.type_besoin, t.nom) AS type_besoin,
                        COALESCE(b.created_at, b.date_saisie) AS created_at,
                        COALESCE(b.quantite, 0) AS quantite,
                        COALESCE(b.prix_unitaire, 0) AS prix_unitaire,
                        (COALESCE(b.quantite, 0) - COALESCE((SELECT SUM(quantite_attribuee) FROM attributions WHERE besoin_id = b.id), 0)) AS quantite_restante,
                        b.ville_id
                 FROM besoins b
                 LEFT JOIN villes v ON b.ville_id = v.id
                 LEFT JOIN types_besoins t ON b.type_id = t.id
                 WHERE (COALESCE(b.quantite, 0) - COALESCE((SELECT SUM(quantite_attribuee) FROM attributions WHERE besoin_id = b.id), 0)) > 0
                 ORDER BY COALESCE(b.created_at, b.date_saisie) ASC";

        $besoinsRows = $db->fetchAll($sqlB);
        $besoins = [];
        foreach ($besoinsRows as $r) {
            $r['quantite_restante'] = (float) $r['quantite_restante'];
            $r['quantite'] = (float) $r['quantite'];
            $r['prix_unitaire'] = (float) $r['prix_unitaire'];
            $besoins[$r['id']] = $r;
        }

        // Récupérer dons avec quantite restante (pour l'instant on calcule remaining en unités pour non-argent,
        // et en montant pour 'argent')
        $sqlD = "SELECT d.*, v.nom AS ville_nom,
                        COALESCE(d.quantite, 0) AS quantite,
                        COALESCE(d.prix_unitaire, 0) AS prix_unitaire,
                        COALESCE(d.date_saisie, d.created_at) AS date_saisie,
                        (COALESCE(d.quantite, 0) - COALESCE((SELECT SUM(quantite_attribuee) FROM attributions WHERE don_id = d.id), 0)) AS quantite_restante
                 FROM dons d
                 LEFT JOIN villes v ON d.ville_id = v.id
                 WHERE (COALESCE(d.quantite, 0) - COALESCE((SELECT SUM(quantite_attribuee) FROM attributions WHERE don_id = d.id), 0)) > 0
                 ORDER BY COALESCE(d.date_saisie, d.created_at) ASC";

        $donsRows = $db->fetchAll($sqlD);

        $allocations = [];
        $totalAllocatedMoney = 0.0;

        // Helper: trouver besoins éligibles pour un don donné
        $matchEligible = function(array $don, array $besoinsList) {
            $eligible = [];
            foreach ($besoinsList as $b) {
                if ($b['quantite_restante'] <= 0) continue;

                // si don a une ville, privilégier même ville
                if (!is_null($don['ville_id']) && $don['ville_id'] != $b['ville_id']) continue;

                if ($don['type_don'] === 'argent') {
                    // l'argent peut acheter 'nature' et 'materiau'
                    if (in_array($b['type_besoin'], ['nature','materiau'])) {
                        $eligible[] = $b;
                    }
                } else {
                    // don matériel/naturel -> doit matcher le même type
                    if ($b['type_besoin'] !== $don['type_don']) continue;

                    // prioriser correspondance de désignation (insensible à la casse)
                    $descDon = mb_strtolower(trim($don['designation'] ?? ''));
                    $descBesoin = mb_strtolower(trim($b['designation'] ?? ''));
                    if ($descDon !== '' && ($descDon === $descBesoin || mb_stripos($descBesoin, $descDon) !== false || mb_stripos($descDon, $descBesoin) !== false)) {
                        // exact / partiel
                        $eligible[] = $b;
                    } else {
                        // pour que le don soit attribué même si la désignation n'est pas identique,
                        // on autorise le matching par type si aucune correspondance exacte trouvée plus tard.
                        $eligible[] = $b;
                    }
                }
            }
            // trier par date (FIFO)
            usort($eligible, function($a, $b) { return strtotime($a['created_at']) <=> strtotime($b['created_at']); });
            return $eligible;
        };

        // Parcours des dons (ordre de saisie)
        foreach ($donsRows as $don) {
            $donType = $don['type_don'];

            if ($donType === 'argent') {
                // montant disponible en Ariary (quantite * prix_unitaire) - montant déjà utilisé (approx.)
                $originalMoney = floatval($don['quantite']) * floatval($don['prix_unitaire']);

                // estimer le montant déjà dépensé pour ce don (JOIN attributions->besoins)
                $spentRow = $db->fetchRow(
                    "SELECT COALESCE(SUM(a.quantite_attribuee * b.prix_unitaire),0) AS spent
                     FROM attributions a
                     JOIN besoins b ON a.besoin_id = b.id
                     WHERE a.don_id = ?",
                    [ $don['id'] ]
                );
                $spent = floatval($spentRow['spent'] ?? 0);
                $moneyAvailable = max(0.0, $originalMoney - $spent);

                if ($moneyAvailable <= 0) continue;

                $eligible = $matchEligible($don, $besoins);

                foreach ($eligible as &$b) {
                    if ($b['quantite_restante'] <= 0) continue;
                    // coût par unité en tenant compte du frais
                    $costPerUnit = $b['prix_unitaire'] * (1 + $feePct / 100.0);
                    if ($costPerUnit <= 0) continue;

                    // quantité maximale finançable par l'argent restant
                    $maxQtyByMoney = $moneyAvailable / $costPerUnit;
                    if ($maxQtyByMoney <= 0) continue;

                    $qty = min($b['quantite_restante'], $maxQtyByMoney);
                    // arrondir à 2 décimales (les quantités peuvent être décimales)
                    $qty = round($qty, 2);
                    if ($qty <= 0) continue;

                    $montant = $qty * $b['prix_unitaire'] * (1 + $feePct / 100.0);
                    $montant = round($montant, 2);

                    $allocations[] = [
                        'don_id' => $don['id'],
                        'don_designation' => $don['designation'],
                        'don_type' => $donType,
                        'don_ville_id' => $don['ville_id'],
                        'besoin_id' => $b['id'],
                        'besoin_designation' => $b['designation'],
                        'besoin_type' => $b['type_besoin'],
                        'quantite_attribuee' => $qty,
                        'montant_attribue' => $montant
                    ];

                    // diminuer les reste
                    $b['quantite_restante'] -= $qty;
                    $moneyAvailable -= $montant;
                    $totalAllocatedMoney += $montant;

                    if ($moneyAvailable <= 0.01) break; // plus rien
                }
                unset($b);

            } else {
                // don en nature/matériau : quantité disponible en unités (tenons compte des attributions existantes)
                $unitsAvailable = floatval($don['quantite']) - floatval($don['quantite_restante']) + floatval($don['quantite_restante']);
                // Note: la requête SQL ci-dessus fournit déjà quantite_restante (non attribuée). On s'en sert.
                $unitsAvailable = floatval($don['quantite_restante']);
                if ($unitsAvailable <= 0) continue;

                $eligible = $matchEligible($don, $besoins);

                foreach ($eligible as &$b) {
                    if ($b['quantite_restante'] <= 0) continue;
                    if ($unitsAvailable <= 0) break;

                    $qty = min($b['quantite_restante'], $unitsAvailable);
                    $qty = round($qty, 2);
                    if ($qty <= 0) continue;

                    $montant = round($qty * $b['prix_unitaire'], 2);

                    $allocations[] = [
                        'don_id' => $don['id'],
                        'don_designation' => $don['designation'],
                        'don_type' => $donType,
                        'don_ville_id' => $don['ville_id'],
                        'besoin_id' => $b['id'],
                        'besoin_designation' => $b['designation'],
                        'besoin_type' => $b['type_besoin'],
                        'quantite_attribuee' => $qty,
                        'montant_attribue' => $montant
                    ];

                    $b['quantite_restante'] -= $qty;
                    $unitsAvailable -= $qty;
                    $totalAllocatedMoney += $montant;
                }
                unset($b);
            }
        }

        // Calculs récapitulatifs
        $totalNeeds = 0.0;
        $totalNeedsRemaining = 0.0;
        foreach ($besoins as $b) {
            $totalNeeds += $b['quantite'] * $b['prix_unitaire'];
            $totalNeedsRemaining += max(0.0, $b['quantite_restante'] * $b['prix_unitaire']);
        }

        return [
            'allocations' => $allocations,
            'totals' => [
                'needs_total_amount' => round($totalNeeds, 2),
                'allocated_amount' => round($totalAllocatedMoney, 2),
                'needs_remaining_amount' => round($totalNeedsRemaining, 2),
                'count_allocations' => count($allocations)
            ]
        ];
    }
}
