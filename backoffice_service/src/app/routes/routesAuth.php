<?php

use reunionou\backoffice\app\controller\BackOfficeAuthController as BackOfficeAuthController;
use reunionou\backoffice\app\middleware\Middleware as Middleware;

// routes to resolve CORS headers issue
$app->options('/{routes:.+}', function ($request, $response, $args) {
    return $response;
});

$app->add(Middleware::class .':corsHeaders');


// routes of auth service
$app->post('/auth[/]', BackOfficeAuthController::class . ':authenticate')
    ->setName('authentification');

$app->post('/users[/]', BackOfficeAuthController::class . ':createUser')
    ->setName('createUser');

