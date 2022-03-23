<?php

require_once  __DIR__ . '/../src/vendor/autoload.php';

use reunionou\auth\app\controller\AuthController as AuthController;
use reunionou\auth\app\middleware\Middleware;
use reunionou\auth\app\validators\Validators as Validators;
use \DavidePastore\Slim\Validation\Validation as Validation ;

$settings = require_once __DIR__. '/../src/app/conf/settings.php';

$app_config = array_merge($settings);


$app = new \Slim\App(new \Slim\Container($app_config));

// Initiate DB connection with Eloquent
$capsule = new \Illuminate\Database\Capsule\Manager;
$capsule->addConnection($app_config['settings']['dbfile']);
$capsule->bootEloquent();
$capsule->setAsGlobal();

// Set the differents routes

$app->options('/{routes:.+}', function ($request, $response, $args) {
    return $response;
});

$app->add(Middleware::class .':corsHeaders');
 
$app->post('/auth[/]', AuthController::class . ':authenticate')
    ->setName('authentification');

$app->get('/check[/]', AuthController::class . ':check')
    ->setName('check');

$app->post('/create[/]', AuthController::class . ':createAccount')
    ->add(Middleware::class . ':createID')
    ->add(new Validation(Validators::validators_createUser()))
    ->setName('createAccount');

$app->put('/update/{id}[/]', AuthController::class . ':updateAccount')
    ->setName('updateAccount');

$app->get('/users/{id}[/]', AuthController::class . ':getUser')
    ->setName('getUser');


$app->map(['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], '/{routes:.+}', function($req, $res) {
        $handler = $this->notFoundHandler; // handle using the default Slim page not found handler
        return $handler($req, $res);
    });

$app->run();