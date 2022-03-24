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

// delte event : delete assocaited tables

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

$app->delete('/events/{id}[/]', Events_Controller::class . ':deleteEventById')
    ->setName('deleteEventById'); //todo compléter : supprimer tout message et member associés

//TODO evetually : creator of event can delete his/her event (côté backoffice?)

 //TODO get event(s) by id creator

 //TODO deleteEventExpired
    

    
// Messages

//todo : (put ? delete ?)

$app->get('/messages/{id}[/]', Messages_Controller::class . ':getMessage')
    ->setName('getMessage');


$app->get('/events/{id}/messages[/]', Messages_Controller::class . ':getMessagesByEvent')
    ->setName('getMessagesByEvent');

$app->post('/messages[/]', Messages_Controller::class . ':createMessage')
    ->setName('createMessage')
    ->add(new Validation($messageValidators));

    //todo deleteMessagesByEventID
  
//todo ?? message by members. Dans ce cas, rajouter ce lien dans hateos dans getMember.

// Members

//TODO delete membersByEventID

$app->post('/members[/]', Members_Controller::class . ':createMember')
    ->setName('createMember');// todo validator

$app->put('/members/{id}[/]', Members_Controller::class . ':updateMember')
    ->setName('updateMember');// todo validator //? USELESS 
    

$app->get('/members/{id}[/]', Members_Controller::class . ':getMember')
    ->setName('getMember');
    
$app->get('/events/{id}/members[/]', Members_Controller::class . ':getMembersByEvent')
    ->setName('getMembersByEvent');
    
$app->delete('/members/{id}[/]', Members_Controller::class . ':deleteMemberById')
    ->setName('deleteMemberById');

// GetMember avec pseudo
    // post delete

//TODO tout les events pour un members.user_id : nackoffice rajouter /user/id
//TODO tous les event pour un pseudo
