<?php

use app\controllers\ApiExampleController;
use app\controllers\BesoinController;
use app\controllers\DonController;
use app\middlewares\SecurityHeadersMiddleware;
use flight\Engine;
use flight\net\Router;

/** 
 * @var Router $router 
 * @var Engine $app
 */

// This wraps all routes in the group with the SecurityHeadersMiddleware
$router->group('', function(Router $router) use ($app) {

	// Page d'accueil
	$router->get('/', function() use ($app) {
		$app->render('welcome', [ 'message' => 'Bienvenue sur l\'application BNGRC - Gestion des dons' ]);
	});

	// Exemples de routes
	$router->get('/hello-world/@name', function($name) {
		echo '<h1>Hello world! Oh hey '.$name.'!</h1>';
	});

	// Routes API (exemples)
	$router->group('/api', function() use ($router) {
		$router->get('/users', [ ApiExampleController::class, 'getUsers' ]);
		$router->get('/users/@id:[0-9]', [ ApiExampleController::class, 'getUser' ]);
		$router->post('/users/@id:[0-9]', [ ApiExampleController::class, 'updateUser' ]);
	});

	// ============================================
	// ROUTES CRUD BESOINS
	// ============================================
	
	// Liste des besoins
	$router->get('/besoins', [ BesoinController::class, 'index' ]);
	
	// Afficher le formulaire de création
	$router->get('/besoins/create', [ BesoinController::class, 'create' ]);
	
	// Enregistrer un nouveau besoin
	$router->post('/besoins/store', [ BesoinController::class, 'store' ]);
	
	// Afficher le formulaire d'édition
	$router->get('/besoins/edit/@id:[0-9]+', [ BesoinController::class, 'edit' ]);
	
	// Mettre à jour un besoin
	$router->post('/besoins/update/@id:[0-9]+', [ BesoinController::class, 'update' ]);
	
	// Supprimer un besoin
	$router->post('/besoins/delete/@id:[0-9]+', [ BesoinController::class, 'delete' ]);

	// ============================================
	// ROUTES DONS (à implémenter)
	// ============================================
	
	// Routes pour la gestion des dons
	$router->get('/dons', [ DonController::class, 'index' ]);
	$router->get('/dons/create', [ DonController::class, 'create' ]);
	$router->post('/dons/store', [ DonController::class, 'store' ]);
	
}, [ SecurityHeadersMiddleware::class ]);
