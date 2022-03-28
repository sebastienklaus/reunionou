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
        
$app->get('/members/{pseudo}/events[/]', Events_Controller::class . ':getEventByMemberPseudo')
    ->setName('getEventByMemberPseudo');

$app->get('/events/{id}[/]', Events_Controller::class . ':getEvent')
    ->setName('getEvent');

$app->get('/events[/]', Events_Controller::class . ':getAllEvent')    
    ->setName('getAllEvent');

$app->delete('/events/{id}[/]', Events_Controller::class . ':deleteEventById')
    ->setName('deleteEventById');

$app->get('/users/{id}/created_events[/]', Events_Controller::class . ':getEventCreatedByUserId')
    ->setName('getEventCreatedByUserId');
    
$app->get('/members_users/{id}/events[/]', Events_Controller::class . ':getAllEventsByMember')
    ->setName('getAllEventsByMember');

$app->get('/users/{id}/events[/]', Events_Controller::class . ':getAllEventsByUserId')
    ->setName('getAllEventsByUserId');

 //TODO evetually : creator of event can delete his/her event (côté backoffice?)

 //TODO deleteEventExpired
    

    
// Messages

//todo : (put ?)

$app->get('/messages/{id}[/]', Messages_Controller::class . ':getMessage')
    ->setName('getMessage');


$app->get('/events/{id}/messages[/]', Messages_Controller::class . ':getMessagesByEvent')
    ->setName('getMessagesByEvent');

$app->post('/messages[/]', Messages_Controller::class . ':createMessage')
    ->setName('createMessage')
    ->add(new Validation($messageValidators));

$app->delete('/messages/{id}[/]', Messages_Controller::class . ':deleteMessageById')
    ->setName('deleteMessageById');

$app->delete('/events/{id}/messages[/]', Messages_Controller::class . ':deleteMessagesByEvent')
    ->setName('deleteMessagesByEvent');
  
//todo ?? message By members. Dans ce cas, rajouter ce lien dans hateos dans getMember.

// Members

$app->post('/members[/]', Members_Controller::class . ':createMember')
    ->setName('createMember')
    ->add(new Validation($memberValidators));

$app->put('/members/{id}[/]', Members_Controller::class . ':updateMember')
    ->setName('updateMember')
    ->add(new Validation($memberValidators));


$app->get('/users/{id}/members[/]', Members_Controller::class . ':getMembersByUserId')
    ->setName('getMembersByUserId');
    
    
$app->get('/members/{id}[/]', Members_Controller::class . ':getMember')
    ->setName('getMember');
    
$app->get('/events/{id}/members[/]', Members_Controller::class . ':getMembersByEvent')
    ->setName('getMembersByEvent');
    
$app->delete('/members/{id}[/]', Members_Controller::class . ':deleteMemberById')
    ->setName('deleteMemberById');

$app->delete('/events/{id}/members[/]', Members_Controller::class . ':deleteMembersByEvent')
    ->setName('deleteMembersByEvent');

// GetMember avec pseudo
    // post delete

//TODO tout les events pour un members.user_id : nackoffice rajouter /user/id

$app->get('/member[/]', Members_Controller::class . ':getOneMember')
    ->setName('getOneMember');