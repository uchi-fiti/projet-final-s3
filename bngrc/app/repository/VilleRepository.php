<?php 
namespace app\repository;
use flight\Engine;
use Flight;
use PDO;
class VilleRepository {
    private $pdo;
    public function __construct($pdo) { 
        $this->pdo = $pdo;
    }
    public function getVilleDetailedInfo()
    {
        $stmt = $this->pdo->query("
            SELECT 
                v.id,
                v.nom AS ville,
                COALESCE(SUM(b.quantite * b.prix_unitaire), 0) AS besoin_total,
                COALESCE(SUM(a.quantite_attribuee * b.prix_unitaire), 0) AS total_attribue
            FROM villes v
            LEFT JOIN besoins b ON b.ville_id = v.id
            LEFT JOIN attributions a ON a.besoin_id = b.id
            GROUP BY v.id, v.nom
            ORDER BY v.nom ASC
        ");

        $villes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $villes;
    }

    public function getDashboardStats()
    {
        // Total besoins (valeur monétaire)
        $stmt = $this->pdo->query("SELECT COALESCE(SUM(quantite * prix_unitaire), 0) AS total FROM besoins");
        $totalBesoins = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Total dons (valeur monétaire)
        $stmt = $this->pdo->query("SELECT COALESCE(SUM(montant_total), 0) AS total FROM dons");
        $totalDons = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Total distribué (valeur monétaire des attributions)
        $stmt = $this->pdo->query("
            SELECT COALESCE(SUM(a.quantite_attribuee * b.prix_unitaire), 0) AS total
            FROM attributions a
            JOIN besoins b ON a.besoin_id = b.id
        ");
        $totalDistribue = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Taux de couverture
        $tauxCouverture = $totalBesoins > 0 ? round(($totalDistribue / $totalBesoins) * 100) : 0;

        return [
            'total_besoins' => $totalBesoins,
            'total_dons' => $totalDons,
            'total_distribue' => $totalDistribue,
            'taux_couverture' => $tauxCouverture
        ];
    }
}





?>