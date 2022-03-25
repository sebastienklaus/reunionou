<?php

use reunionou\backoffice\app\controller\BackOfficeMessagesController;
// Set the differents routes

$app->get('/messages/{id}[/]',BackOfficeMessagesController::class . ':getMessage')
    ->setName('getMessage');

$app->get('/events/{id}/messages[/]', BackOfficeMessagesController::class . ':getMessagesByEvent')
    ->setName('getMessagesByEvent');

$app->post('/messages[/]', BackOfficeMessagesController::class . ':createMessage')
    ->setName('createMessage');

$app->delete('/messages/{id}[/]', BackOfficeMessagesController::class . ':deleteMessageById')
    ->setName('deleteMessageById');
