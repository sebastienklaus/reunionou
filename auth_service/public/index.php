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



$app->get('/users[/]', AuthController::class . ':getUsers')
    ->setName('getUsers');

$app->get('/users/{id}[/]', AuthController::class . ':getUserByID')
    ->setName('getUserByID');

    
    
$app->get('/checkAdmin[/]', AuthController::class . ':checkAdmin')
    ->setName('checkAdmin');

$app->run();