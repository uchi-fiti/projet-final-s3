<?php

namespace app\services;

use PDO;
use Exception;

class AchatService
{
    /**
     * Simule un achat via dons en argent.
     * Ne modifie RIEN en base de données – renvoie uniquement le résultat calculé.
     *
     * @param PDO    $pdo
     * @param int    $besoin_id
     * @param int    $quantite       Quantité à acheter
     * @param float  $frais_percent  Pourcentage de frais (0-100)
     * @return array ['success' => bool, 'message' => string, ...données de simulation]
     */
    public static function simulateAchat(PDO $pdo, int $besoin_id, int $quantite, float $frais_percent): array
    {
        // 1. Récupérer le besoin
        $stmt = $pdo->prepare("
            SELECT b.*, v.nom AS ville_nom, t.nom AS type_nom
            FROM bngrc_besoins b
            JOIN bngrc_villes v ON b.ville_id = v.id
            JOIN bngrc_types_besoins t ON b.type_id = t.id
            WHERE b.id = ?
        ");
        $stmt->execute([$besoin_id]);
        $besoin = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$besoin) {
            return ['success' => false, 'message' => 'Besoin introuvable.'];
        }

        // 2. Vérifier quantité demandée <= quantité restante
        if ($quantite <= 0) {
            return ['success' => false, 'message' => 'La quantité doit être supérieure à 0.'];
        }

        if ($quantite > $besoin['quantite_restante']) {
            return [
                'success' => false,
                'message' => "Quantité demandée ($quantite) supérieure à la quantité restante ({$besoin['quantite_restante']})."
            ];
        }

        // 3. Vérifier qu'il n'y a plus de dons directs du même type disponibles
        $stmtDirectDons = $pdo->prepare("
            SELECT COALESCE(SUM(quantite_restante), 0) AS dons_directs_restants
            FROM bngrc_dons
            WHERE type_id = ? AND quantite_restante > 0
        ");
        $stmtDirectDons->execute([$besoin['type_id']]);
        $donsDirects = $stmtDirectDons->fetch(PDO::FETCH_ASSOC)['dons_directs_restants'];

        if ($donsDirects > 0) {
            return [
                'success' => false,
                'message' => "Il reste $donsDirects unités de dons directs de type \"{$besoin['type_nom']}\" disponibles. Lancez d'abord le dispatch automatique avant d'acheter."
            ];
        }

        // 4. Calculer les montants
        $montant_unitaire = (float) $besoin['prix_unitaire'];
        $montant_base     = $quantite * $montant_unitaire;
        $montant_frais    = round($montant_base * $frais_percent / 100, 2);
        $montant_total    = $montant_base + $montant_frais;

        // 5. Vérifier fonds argent disponibles
        $stmtArgent = $pdo->prepare("
            SELECT COALESCE(SUM(montant_restant), 0) AS fonds_disponibles
            FROM bngrc_dons d
            JOIN bngrc_types_besoins t ON d.type_id = t.id
            WHERE t.nom = 'Argent' AND d.montant_restant > 0
        ");
        $stmtArgent->execute();
        $fondsDisponibles = (float) $stmtArgent->fetch(PDO::FETCH_ASSOC)['fonds_disponibles'];

        if ($montant_total > $fondsDisponibles) {
            return [
                'success' => false,
                'message' => "Fonds insuffisants. Montant requis : " . number_format($montant_total, 2, ',', ' ') .
                             " Ar. Fonds disponibles : " . number_format($fondsDisponibles, 2, ',', ' ') . " Ar."
            ];
        }

        // 6. Succès – retourner la simulation
        return [
            'success'           => true,
            'message'           => 'Simulation réussie.',
            'besoin_id'         => $besoin_id,
            'besoin'            => $besoin,
            'quantite'          => $quantite,
            'montant_unitaire'  => $montant_unitaire,
            'frais_pourcentage' => $frais_percent,
            'montant_base'      => $montant_base,
            'montant_frais'     => $montant_frais,
            'montant_total'     => $montant_total,
            'fonds_disponibles' => $fondsDisponibles,
            'fonds_apres_achat' => $fondsDisponibles - $montant_total,
        ];
    }

    /**
     * Valide et exécute un achat via dons en argent.
     * Modifie la base de données (dans une transaction).
     *
     * @param PDO    $pdo
     * @param int    $besoin_id
     * @param int    $quantite
     * @param float  $frais_percent
     * @return array ['success' => bool, 'message' => string]
     */
    public static function validerAchat(PDO $pdo, int $besoin_id, int $quantite, float $frais_percent): array
    {
        // Re-simuler pour valider les conditions
        $simulation = self::simulateAchat($pdo, $besoin_id, $quantite, $frais_percent);

        if (!$simulation['success']) {
            return $simulation;
        }

        $montant_total   = $simulation['montant_total'];
        $montant_base    = $simulation['montant_base'];
        $montant_frais   = $simulation['montant_frais'];
        $montant_unitaire = $simulation['montant_unitaire'];

        try {
            $pdo->beginTransaction();

            // 1. Insérer l'achat
            $stmtInsert = $pdo->prepare("
                INSERT INTO bngrc_achats (besoin_id, quantite, montant_unitaire, frais_pourcentage, montant_base, montant_frais, montant_total)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $stmtInsert->execute([
                $besoin_id,
                $quantite,
                $montant_unitaire,
                $frais_percent,
                $montant_base,
                $montant_frais,
                $montant_total
            ]);

            // 2. Déduire des dons en argent (FIFO – les plus anciens d'abord)
            $stmtArgentDons = $pdo->prepare("
                SELECT d.id, d.montant_restant
                FROM bngrc_dons d
                JOIN bngrc_types_besoins t ON d.type_id = t.id
                WHERE t.nom = 'Argent' AND d.montant_restant > 0
                ORDER BY d.date_saisie ASC
            ");
            $stmtArgentDons->execute();
            $argentDons = $stmtArgentDons->fetchAll(PDO::FETCH_ASSOC);

            $restant = $montant_total;

            foreach ($argentDons as $don) {
                if ($restant <= 0) break;

                $montantDispo = (float) $don['montant_restant'];
                $deduction = min($restant, $montantDispo);

                $stmtUpdateDon = $pdo->prepare("
                    UPDATE bngrc_dons SET montant_restant = montant_restant - ? WHERE id = ?
                ");
                $stmtUpdateDon->execute([$deduction, $don['id']]);

                $restant -= $deduction;
            }

            // 3. Mettre à jour quantite_restante du besoin
            $stmtUpdateBesoin = $pdo->prepare("
                UPDATE bngrc_besoins SET quantite_restante = quantite_restante - ? WHERE id = ?
            ");
            $stmtUpdateBesoin->execute([$quantite, $besoin_id]);

            $pdo->commit();

            return [
                'success' => true,
                'message' => "Achat validé avec succès. Montant total : " . number_format($montant_total, 2, ',', ' ') . " Ar."
            ];

        } catch (Exception $e) {
            $pdo->rollBack();
            return [
                'success' => false,
                'message' => "Erreur lors de la validation : " . $e->getMessage()
            ];
        }
    }
}
