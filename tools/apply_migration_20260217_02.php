<?php
// Apply SQL migration 20260217-02-add-attributions-batchid-and-logs.sql using DB config from bngrc
// load DB settings by parsing the return array from config.php (avoid requiring Flight)
$cfgContent = file_get_contents(__DIR__ . '/../bngrc/app/config/config.php');
$start = strpos($cfgContent, 'return [');
$end = strrpos($cfgContent, '];');
if ($start === false || $end === false) {
    echo "Unable to parse config.php to obtain DB settings\n"; exit(1);
}
$arrCode = substr($cfgContent, $start + strlen('return '), $end - ($start + strlen('return ')) + 1);
$config = eval('return ' . $arrCode . ';');
$dsn = 'mysql:host='.$config['database']['host'].';dbname='.$config['database']['dbname'].';charset=utf8mb4';
try {
    $pdo = new PDO($dsn, $config['database']['user'], $config['database']['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    echo "DB connect error: " . $e->getMessage() . "\n";
    exit(1);
}

$sql = file_get_contents(__DIR__ . '/../sql/20260217-02-add-attributions-batchid-and-logs.sql');
// split by semicolon followed by newline (simple but works for this file)
$stmts = preg_split('/;[\r\n]+/', $sql);
foreach ($stmts as $s) {
    $s = trim($s);
    if (!$s) continue;
    try {
        $pdo->exec($s);
        echo "OK: " . substr($s, 0, 80) . "\n";
    } catch (PDOException $ex) {
        echo "SQL ERROR: " . $ex->getMessage() . "\n";
    }
}

echo "MIGRATION SCRIPT FINISHED\n";
