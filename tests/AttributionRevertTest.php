<?php

require_once __DIR__ . '/../bngrc/app/repository/AttributionRepository.php';

use PHPUnit\Framework\TestCase;
use app\repository\AttributionRepository;

class AttributionRevertTest extends TestCase
{
    public function testRevertLastBatchRestoresQuantitiesAndDeletesAttributions()
    {
        $pdo = new PDO('sqlite::memory:');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // minimal schema for test
        $pdo->exec("CREATE TABLE bngrc_besoins (id INTEGER PRIMARY KEY AUTOINCREMENT, quantite INTEGER NOT NULL, quantite_restante INTEGER NOT NULL)");
        $pdo->exec("CREATE TABLE bngrc_dons (id INTEGER PRIMARY KEY AUTOINCREMENT, quantite INTEGER NOT NULL, quantite_restante INTEGER NOT NULL)");
        $pdo->exec("CREATE TABLE bngrc_attributions (id INTEGER PRIMARY KEY AUTOINCREMENT, besoin_id INTEGER, don_id INTEGER, quantite_attribuee INTEGER, date_attribution TEXT, batch_id TEXT)");
        $pdo->exec("CREATE TABLE bngrc_action_logs (id INTEGER PRIMARY KEY AUTOINCREMENT, action TEXT, payload TEXT, created_at TEXT)");

        // seed
        $pdo->exec("INSERT INTO bngrc_besoins (quantite, quantite_restante) VALUES (100, 50)");
        $pdo->exec("INSERT INTO bngrc_dons (quantite, quantite_restante) VALUES (100, 30)");
        $pdo->exec("INSERT INTO bngrc_attributions (besoin_id, don_id, quantite_attribuee, date_attribution, batch_id) VALUES (1,1,10,'2026-02-17 10:00:00','b1')");

        $repo = new AttributionRepository();
        $repo->revertLastBatch($pdo);

        $stmt = $pdo->query("SELECT quantite_restante FROM bngrc_besoins WHERE id = 1");
        $this->assertEquals(60, (int)$stmt->fetchColumn());

        $stmt = $pdo->query("SELECT quantite_restante FROM bngrc_dons WHERE id = 1");
        $this->assertEquals(40, (int)$stmt->fetchColumn());

        $stmt = $pdo->query("SELECT COUNT(*) FROM bngrc_attributions");
        $this->assertEquals(0, (int)$stmt->fetchColumn());

        $stmt = $pdo->query("SELECT COUNT(*) FROM bngrc_action_logs");
        $this->assertEquals(1, (int)$stmt->fetchColumn());
    }
}
