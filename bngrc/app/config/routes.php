<?php

use app\controllers\ApiExampleController;
use app\controllers\DonController;
use app\controllers\BesoinController;
use app\controllers\DispatchController;
use app\middlewares\SecurityHeadersMiddleware;
use flight\Engine;
use flight\net\Router;

/** 
 * @var Router $router 
 * @var Engine $app
 */

// This wraps all routes in the group with the SecurityHeadersMiddleware
$router->group('', function(Router $router) use ($app) {

	$router->get('/', function() use ($app) {
		$app->render('welcome', [ 'message' => 'You are gonna do great things!' ]);
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

$router->get("/crud/dons", [DonController::class, 'showDon']);
$router->post("/dons/create", [DonController::class, 'createDon']);
$router->get("/dons/@id:[0-9]+", [DonController::class, 'getDon']);
$router->post("/dons/@id:[0-9]+/update", [DonController::class, 'updateDon']);
$router->post("/dons/@id:[0-9]+/delete", [DonController::class, 'deleteDon']);

$router->get("/dispatch/simulate", [DispatchController::class, 'simulateDispatch']);

$router->get('/attributions', function() use ($app) {
	$app->render('attributions/attributions');
});