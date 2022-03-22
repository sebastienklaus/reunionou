<?php

use DavidePastore\Slim\Validation\Validation;
use reu\events\app\controller\Events_Controller;
use reu\events\app\middleware\CommandeValidator;
use reu\events\app\middleware\Token;


// Events

$app->get('/events/{id}[/]', Events_Controller::class . ':getEvent')
    ->setName('getEvent');

$app->get('/events[/]', Events_Controller::class . ':getAllEvent')
    ->setName('getAllEvent');

    
// Messages

$app->get('/messages/{id}[/]]', Messages_Controller::class . ':getMessage')
    ->setName('getMessage');

// $app->get('/events/{id}/messages[/]', Messages_Controller::class . ':getMessagesByEvent')
//     ->setName('getMessagesByEvent');

// // Members

// $app->get('/events/{id}/members[/]]', Members_Controller::class . ':getMembersByEvent')
//     ->setName('getMembersByEvent');
