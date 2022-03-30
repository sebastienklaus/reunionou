<?php

use reunionou\backoffice\app\controller\BackOfficeMessagesController;
use reunionou\backoffice\app\middleware\Middleware as Middleware;

// Set the differents routes

$app->get('/messages/{id}[/]',BackOfficeMessagesController::class . ':getMessage')
    ->add(Middleware::class . ':checkAuth')
    ->setName('getMessage');

$app->get('/events/{id}/messages[/]', BackOfficeMessagesController::class . ':getMessagesByEvent')
    ->add(Middleware::class . ':checkAuth')
    ->setName('getMessagesByEvent');

$app->post('/messages[/]', BackOfficeMessagesController::class . ':createMessage')
    ->add(Middleware::class . ':checkAuth')
    ->setName('createMessage');

$app->delete('/messages/{id}[/]', BackOfficeMessagesController::class . ':deleteMessageById')
    ->add(Middleware::class . ':checkAuth')
    ->setName('deleteMessageById');
