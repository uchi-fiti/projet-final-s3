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
}





?>