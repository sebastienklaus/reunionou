<?php

use reunionou\backoffice\app\controller\BackOfficeAuthController as BackOfficeAuthController;
use reunionou\backoffice\app\middleware\Middleware as Middleware;



// routes of auth service
$app->post('/auth[/]', BackOfficeAuthController::class . ':authenticate')
    ->setName('authentification');

$app->post('/users[/]', BackOfficeAuthController::class . ':createUser')
    ->setName('createUser');

$app->get('/users[/]', BackOfficeAuthController::class . ':getUsers')
    // ->add(Middleware::class . ':checkAuth')
    ->setName('getUsers');
     
$app->put('/users/{id}[/]', BackOfficeAuthController::class . ':updateUser')
    // ->add(Middleware::class . ':checkAuth')
    ->setName('createUser');
    
$app->get('/users/{id}[/]', BackOfficeAuthController::class . ':getUserById')
    // ->add(Middleware::class . ':checkAuth')    
    ->setName('getUserById');

$app->post('/authAdmin[/]', BackOfficeAuthController::class . ':authenticateAdmin')
    ->add(Middleware::class . ':checkAdmin')
    ->setName('authentification');

