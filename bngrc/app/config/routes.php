<?php

use app\controllers\ApiExampleController;
use app\controllers\DonController;
use app\controllers\DispatchController;
use app\controllers\DashboardController;
use app\controllers\RegionController;
use app\controllers\VilleController;
use app\controllers\TypeController;
use app\controllers\BesoinCrudController;
use app\controllers\AttributionController;
use app\controllers\AchatController;
use app\controllers\RecapController;
use app\middlewares\SecurityHeadersMiddleware;
use flight\Engine;
use flight\net\Router;

/** 
 * @var Router $router 
 * @var Engine $app
 */

// This wraps all routes in the group with the SecurityHeadersMiddleware
$router->group('', function(Router $router) use ($app) {

	$router->get('/', function() {
		Flight::redirect(BASE_URL.'/dashboard');
	});

	$router->get('/hello-world/@name', function($name) {
		echo '<h1>Hello world! Oh hey '.$name.'!</h1>';
	});

	$router->group('/api', function() use ($router) {
		$router->get('/users', [ ApiExampleController::class, 'getUsers' ]);
		$router->get('/users/@id:[0-9]', [ ApiExampleController::class, 'getUser' ]);
		$router->post('/users/@id:[0-9]', [ ApiExampleController::class, 'updateUser' ]);
	});

}, [ SecurityHeadersMiddleware::class ]);

// Dashboard
$router->get("/dashboard", [DashboardController::class, 'index']);

// Regions CRUD
$router->get("/regions", [RegionController::class, 'index']);
$router->post("/regions/create", [RegionController::class, 'create']);
$router->post("/regions/@id:[0-9]+/update", [RegionController::class, 'update']);
$router->post("/regions/@id:[0-9]+/delete", [RegionController::class, 'delete']);

// Villes CRUD
$router->get("/villes", [VilleController::class, 'index']);
$router->post("/villes/create", [VilleController::class, 'create']);
$router->post("/villes/@id:[0-9]+/update", [VilleController::class, 'update']);
$router->post("/villes/@id:[0-9]+/delete", [VilleController::class, 'delete']);

// Types CRUD
$router->get("/types", [TypeController::class, 'index']);
$router->post("/types/create", [TypeController::class, 'create']);
$router->post("/types/@id:[0-9]+/update", [TypeController::class, 'update']);
$router->post("/types/@id:[0-9]+/delete", [TypeController::class, 'delete']);

// Besoins CRUD
$router->get("/besoins", [BesoinCrudController::class, 'index']);
$router->post("/besoins/create", [BesoinCrudController::class, 'create']);
$router->post("/besoins/@id:[0-9]+/update", [BesoinCrudController::class, 'update']);
$router->post("/besoins/@id:[0-9]+/delete", [BesoinCrudController::class, 'delete']);

// Dons CRUD
$router->get("/crud/dons", [DonController::class, 'showDon']);
$router->post("/dons/create", [DonController::class, 'createDon']);
$router->get("/dons/@id:[0-9]+", [DonController::class, 'getDon']);
$router->post("/dons/@id:[0-9]+/update", [DonController::class, 'updateDon']);
$router->post("/dons/@id:[0-9]+/delete", [DonController::class, 'deleteDon']);

// Dispatch simulation
$router->get("/dispatch/simulate", [DispatchController::class, 'simulateDispatch']);
$router->get("/dispatch/proportional", [DispatchController::class, 'simulateProportional']);

// Attributions
$router->get('/attributions', [AttributionController::class, 'index']);

// Achats via dons en argent
$router->get('/besoins-restants', [AchatController::class, 'besoinsRestants']);
$router->get('/achat/@id:[0-9]+', [AchatController::class, 'showAchatForm']);
$router->post('/achat/simulate', [AchatController::class, 'simulate']);
$router->post('/achat/validate', [AchatController::class, 'validate']);

// Récapitulation
$router->get('/recap', [RecapController::class, 'index']);
$router->get('/api/recap', [RecapController::class, 'apiJson']);