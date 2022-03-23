<?php

use DavidePastore\Slim\Validation\Validation;
use reu\events\app\controller\Events_Controller;
use reu\events\app\controller\Messages_Controller;
use reu\events\app\controller\Members_Controller;
use reu\events\app\middleware\EventValidator;
use reu\events\app\middleware\Token;


$validators = EventValidator::create_validators();
// Events
//todo put, post (delete ?)

$app->post('/events[/]', Events_Controller::class . ':createEvent')
    ->setName('createEvent')
    ->add(new Validation($validators));


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

//todo ?? message by members. Dans ce cas, rajouter ce lien dans hateos dans getMember.

// Members

//todo members by id : post put

$app->get('/members/{id}[/]', Members_Controller::class . ':getMember')
    ->setName('getMember');

$app->get('/events/{id}/members[/]', Members_Controller::class . ':getMembersByEvent')
    ->setName('getMembersByEvent');
