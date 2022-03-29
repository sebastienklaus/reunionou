<?php

use reunionou\backoffice\app\controller\BackOfficeMembersController;
// Set the differents routes

$app->post('/members[/]', BackOfficeMembersController::class . ':createMember')
    ->setName('createMember');

$app->put('/members/{id}[/]', BackOfficeMembersController::class . ':updateMember')
    ->setName('updateMember');
    
$app->get('/members/{id}[/]', BackOfficeMembersController::class . ':getMember')
    ->setName('getMember');
    
$app->get('/events/{id}/members[/]', BackOfficeMembersController::class . ':getMembersByEvent')
    ->setName('getMembersByEvent');
    
$app->delete('/members/{id}[/]', BackOfficeMembersController::class . ':deleteMemberById')
    ->setName('deleteMemberById');

$app->get('/users/{id}/members[/]', BackOfficeMembersController::class . ':getMembersByUserId')
    ->setName('getMembersByUserId');
