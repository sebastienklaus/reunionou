<?php

use reunionou\backoffice\app\controller\BackOfficeEventsController;
use reunionou\backoffice\app\middleware\Middleware as Middleware;

// Set the differents routes

$app->post('/events[/]', BackOfficeEventsController::class . ':createEvent')
    ->add(Middleware::class . ':checkAuth') 
    ->setName('createEvent');

$app->put('/events/{id}[/]', BackOfficeEventsController::class . ':updateEvent')
    ->add(Middleware::class . ':checkAuth') 
    ->setName('updateEvent');

$app->get('/events/{id}[/]', BackOfficeEventsController::class . ':getEvent')
    ->add(Middleware::class . ':checkAuth') 
    ->setName('getEvent');

$app->get('/events[/]', BackOfficeEventsController::class . ':getAllEvent')
    ->add(Middleware::class . ':checkAuth') 
    ->setName('getAllEvent');

$app->get('/members/{pseudo}/events[/]', BackOfficeEventsController::class . ':getEventByMemberPseudo')
    ->add(Middleware::class . ':checkAuth') 
    ->setName('getEventByMemberPseudo');

$app->delete('/events/{id}[/]', BackOfficeEventsController::class . ':deleteEventById')
    ->setName('deleteEventById');

$app->get('/users/{id}/created_events[/]', BackOfficeEventsController::class . ':getEventCreatedByUserId')
    ->add(Middleware::class . ':checkAuth') 
    ->setName('getEventCreatedByUserId');

$app->get('/members_users/{id}/events[/]', BackOfficeEventsController::class . ':getAllEventsByMemberId')
    ->add(Middleware::class . ':checkAuth') 
    ->setName('getAllEventsByMemberId');

$app->get('/users/{id}/events[/]', BackOfficeEventsController::class . ':getAllEventsByUserId')
    ->add(Middleware::class . ':checkAuth') 
    ->setName('getAllEventsByUserId');