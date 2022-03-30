<?php

use reunionou\backoffice\app\controller\BackOfficeMembersController;
use reunionou\backoffice\app\middleware\Middleware as Middleware;

// Set the differents routes

$app->post('/members[/]', BackOfficeMembersController::class . ':createMember')
    ->add(Middleware::class . ':checkAuth')
    ->setName('createMember');

$app->put('/members/{id}[/]', BackOfficeMembersController::class . ':updateMember')
    ->add(Middleware::class . ':checkAuth')
    ->setName('updateMember');
    
$app->get('/members/{id}[/]', BackOfficeMembersController::class . ':getMember')
    ->add(Middleware::class . ':checkAuth')
    ->setName('getMember');
    
$app->get('/events/{id}/members[/]', BackOfficeMembersController::class . ':getMembersByEvent')
    ->add(Middleware::class . ':checkAuth')
    ->setName('getMembersByEvent');
    
$app->delete('/members/{id}[/]', BackOfficeMembersController::class . ':deleteMemberById')
    ->add(Middleware::class . ':checkAuth')
    ->setName('deleteMemberById');

$app->get('/users/{id}/members[/]', BackOfficeMembersController::class . ':getMembersByUserId')
    ->add(Middleware::class . ':checkAuth')
    ->setName('getMembersByUserId');

$app->get('/member[/]', BackOfficeMembersController::class . ':getOneMember')
    ->add(Middleware::class . ':checkAuth')
    ->setName('getOneMember');

$app->get('/members[/]', BackOfficeMembersController::class . ':getMembers')
    ->add(Middleware::class . ':checkAuth')
    ->setName('getMembers');
