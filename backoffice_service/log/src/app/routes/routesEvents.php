<?php

use reunionou\backoffice\app\controller\BackOfficeEventsController;
// Set the differents routes

$app->post('/events[/]', BackOfficeEventsController::class . ':createEvent')
    ->setName('createEvent');

$app->put('/events/{id}[/]', BackOfficeEventsController::class . ':updateEvent')
    ->setName('updateEvent');

$app->get('/events/{id}[/]', BackOfficeEventsController::class . ':getEvent')
    ->setName('getEvent');

$app->get('/events[/]', BackOfficeEventsController::class . ':getAllEvent')
    ->setName('getAllEvent');

$app->get('/members/{pseudo}/events[/]', BackOfficeEventsController::class . ':getEventByMemberPseudo')
    ->setName('getEventByMemberPseudo');

$app->delete('/events/{id}[/]', BackOfficeEventsController::class . ':deleteEventById')
    ->setName('deleteEventById');

$app->get('/users/{id}/events[/]', BackOfficeEventsController::class . ':getEventByUserId')
    ->setName('getEventByUserId');