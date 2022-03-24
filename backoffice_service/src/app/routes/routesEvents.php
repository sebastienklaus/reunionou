<?php

use reunionou\backoffice\app\controller\BackOfficeEventsController;
// Set the differents routes
 
$app->post('/events[/]', BackOfficeEventsController::class . ':getAllEvent')
    ->setName('getAllEvent');
 