<?php

use flight\Engine;
use flight\database\PdoWrapper;
use flight\debug\database\PdoQueryCapture;
use flight\debug\tracy\TracyExtensionLoader;
use Tracy\Debugger;

/*********************************************
 *         FlightPHP Service Setup           *
 *********************************************
 * This file registers services and integrations
 * for your FlightPHP application. Edit as needed.
 *
 * @var array  $config  From config.php
 * @var Engine $app     FlightPHP app instance
 **********************************************/

/*********************************************
 *           Tracy Debugger Setup            *
 **********************************************/
Debugger::enable(); // Auto-detects environment
Debugger::$logDirectory = __DIR__ . $ds . '..' . $ds . 'log'; // Log directory
Debugger::$strictMode = true; // Show all errors
if (Debugger::$showBar === true && php_sapi_name() !== 'cli') {
	(new TracyExtensionLoader($app)); // Load FlightPHP Tracy extensions
}

/**********************************************
 *           Database Service Setup           *
 **********************************************/

// Configuration de la base de données MySQL/MariaDB
$dsn = 'mysql:host=' . $config['database']['host'] . ';dbname=' . $config['database']['dbname'] . ';charset=utf8mb4';

// Register Flight::db() service
// En développement, utiliser PdoQueryCapture pour logger les requêtes
// En production, utiliser PdoWrapper pour les performances
$pdoClass = Debugger::$showBar === true ? PdoQueryCapture::class : PdoWrapper::class;

$app->register('db', $pdoClass, [ 
	$dsn, 
	$config['database']['user'] ?? null, 
	$config['database']['password'] ?? null,
	[
		PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
		PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
		PDO::ATTR_EMULATE_PREPARES => false,
	]
]);

/*********************************************
 *           Session Service Setup           *
 **********************************************/
// Activer les sessions si nécessaire
// $app->register('session', \flight\Session::class, [
//     [
//         'prefix' => 'bngrc_session_',
//         'save_path' => PROJECT_ROOT . '/storage/sessions',
//     ]
// ]);
