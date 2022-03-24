<?php

use reunionou\backoffice\app\controller\BackOfficeAuthController as BackOfficeAuthController;

// Set the differents routes
 
$app->post('/auth[/]', BackOfficeAuthController::class . ':authenticate')
    ->setName('authentification');
 