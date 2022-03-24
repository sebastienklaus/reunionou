<?php

use reunionou\backoffice\app\controller\BackOfficeAuthController as BackOfficeAuthController;
use reunionou\backoffice\app\middleware\Middleware as Middleware;



// routes of auth service
$app->post('/auth[/]', BackOfficeAuthController::class . ':authenticate')
    ->setName('authentification');

$app->post('/users[/]', BackOfficeAuthController::class . ':createUser')
    ->setName('createUser');

$app->get('/users[/]', BackOfficeAuthController::class . ':getUsers')
    ->setName('getUsers');
     
$app->put('/users/{id}[/]', BackOfficeAuthController::class . ':updateUser')
    ->setName('createUser');
    
$app->get('/users/{id}[/]', BackOfficeAuthController::class . ':getUserById')
    ->setName('getUserById');

$app->map(['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], '/{routes:.+}', function($req, $res) {
    $handler = $this->notFoundHandler; // handle using the default Slim page not found handler
    return $handler($req, $res);
});