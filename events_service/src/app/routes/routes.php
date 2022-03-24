<?php

use DavidePastore\Slim\Validation\Validation;
use reu\events\app\controller\Events_Controller;
use reu\events\app\controller\Messages_Controller;
use reu\events\app\controller\Members_Controller;
use reu\events\app\middleware\EventValidator;
use reu\events\app\middleware\MessageValidator;
use reu\events\app\middleware\MemberValidator;
use reu\events\app\middleware\Token;


$eventValidators = EventValidator::create_validators();
$messageValidators = MessageValidator::create_validators();
$memberValidators = MemberValidator::create_validators();
// Events

$app->post('/events[/]', Events_Controller::class . ':createEvent')
    ->setName('createEvent')
    ->add(new Validation($eventValidators));

$app->put('/events/{id}[/]', Events_Controller::class . ':updateEvent')
    ->setName('updateEvent')
    ->add(new Validation($eventValidators));


$app->get('/events/{id}[/]', Events_Controller::class . ':getEvent')
    ->setName('getEvent');

$app->get('/events[/]', Events_Controller::class . ':getAllEvent')    
    ->setName('getAllEvent');

//TODO evetually : creator of event can delete his/her event
    

    
// Messages

//todo : post (put ? delete ?)

$app->get('/messages/{id}[/]', Messages_Controller::class . ':getMessage')
    ->setName('getMessage');


$app->get('/events/{id}/messages[/]', Messages_Controller::class . ':getMessagesByEvent')
    ->setName('getMessagesByEvent');

$app->post('/messages[/]', Messages_Controller::class . ':createMessage')
    ->setName('createMessage')
    ->add(new Validation($messageValidators));

//todo ?? message by members. Dans ce cas, rajouter ce lien dans hateos dans getMember.

// Members

//todo members by id : post put

$app->get('/members/{id}[/]', Members_Controller::class . ':getMember')
    ->setName('getMember');

$app->get('/events/{id}/members[/]', Members_Controller::class . ':getMembersByEvent')
    ->setName('getMembersByEvent');


    // post delete