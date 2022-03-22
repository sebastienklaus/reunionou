<?php

use DavidePastore\Slim\Validation\Validation;
use reu\events\app\controller\Events_Controller;
use reu\events\app\middleware\CommandeValidator;
use reu\events\app\middleware\Token;


$app->get('/test[/]', Events_Controller::class . ':test');
