<?php

use DavidePastore\Slim\Validation\Validation;
use reu\events\app\controller\Events_Controller;
use reu\events\app\controller\Messages_Controller;
use reu\events\app\controller\Members_Controller;
use reu\events\app\middleware\CommandeValidator;
use reu\events\app\middleware\Token;


// Events

//todo put, post (delete ?)

$app->get('/events/{id}[/]', Events_Controller::class . ':getEvent')
    ->setName('getEvent');

$app->get('/events[/]', Events_Controller::class . ':getAllEvent')
    ->setName('getAllEvent');

    
// Messages

//todo : post (put ?)

$app->get('/messages/{id}[/]', Messages_Controller::class . ':getMessage')
    ->setName('getMessage');


$app->get('/events/{id}/messages[/]', Messages_Controller::class . ':getMessagesByEvent')
    ->setName('getMessagesByEvent');

// Members

//todo members by id : get post

$app->get('/events/{id}/members[/]', Members_Controller::class . ':getMembersByEvent')
    ->setName('getMembersByEvent');
