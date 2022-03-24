<?php

use reunionou\backoffice\app\controller\BackOfficeEventsController;
// Set the differents routes
 

$app->get('/events/{id}[/]', BackOfficeEventsController::class . ':getEvent')
    ->setName('getEvent');

$app->get('/events[/]', BackOfficeEventsController::class . ':getAllEvent')
->setName('getAllEvent');

$app->post('/events[/]', BackOfficeEventsController::class . ':createEvent')
    ->setName('createEvent');

$app->put('/events/{id}[/]', BackOfficeEventsController::class . ':updateEvent')
    ->setName('updateEvent');