<?php
require_once __DIR__ . '/../bngrc/app/repository/AttributionRepository.php';

use app\repository\AttributionRepository;

$pdo = new PDO('sqlite::memory:');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$pdo->exec("CREATE TABLE bngrc_besoins (id INTEGER PRIMARY KEY AUTOINCREMENT, quantite INTEGER NOT NULL, quantite_restante INTEGER NOT NULL)");
$pdo->exec("CREATE TABLE bngrc_dons (id INTEGER PRIMARY KEY AUTOINCREMENT, quantite INTEGER NOT NULL, quantite_restante INTEGER NOT NULL)");
$pdo->exec("CREATE TABLE bngrc_attributions (id INTEGER PRIMARY KEY AUTOINCREMENT, besoin_id INTEGER, don_id INTEGER, quantite_attribuee INTEGER, date_attribution TEXT, batch_id TEXT)");
$pdo->exec("CREATE TABLE bngrc_action_logs (id INTEGER PRIMARY KEY AUTOINCREMENT, action TEXT, payload TEXT, created_at TEXT)");

$pdo->exec("INSERT INTO bngrc_besoins (quantite, quantite_restante) VALUES (100, 50)");
$pdo->exec("INSERT INTO bngrc_dons (quantite, quantite_restante) VALUES (100, 30)");
$pdo->exec("INSERT INTO bngrc_attributions (besoin_id, don_id, quantite_attribuee, date_attribution, batch_id) VALUES (1,1,10,'2026-02-17 10:00:00','b1')");

$repo = new AttributionRepository();
try {
    $repo->revertLastBatch($pdo);
    $b = $pdo->query("SELECT quantite_restante FROM bngrc_besoins WHERE id = 1")->fetchColumn();
    $d = $pdo->query("SELECT quantite_restante FROM bngrc_dons WHERE id = 1")->fetchColumn();
    $cntAttr = $pdo->query("SELECT COUNT(*) FROM bngrc_attributions")->fetchColumn();
    $cntLog = $pdo->query("SELECT COUNT(*) FROM bngrc_action_logs")->fetchColumn();

    echo "RESULT: besoin.quantite_restante={$b}, don.quantite_restante={$d}, attributions_remaining={$cntAttr}, action_logs={$cntLog}\n";
    if ((int)$b === 60 && (int)$d === 40 && (int)$cntAttr === 0 && (int)$cntLog === 1) {
        echo "SMOKE TEST: OK\n";
        exit(0);
    }
    echo "SMOKE TEST: FAILED\n";
    exit(2);
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(3);
}
