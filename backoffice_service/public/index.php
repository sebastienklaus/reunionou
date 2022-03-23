<?php

require_once  __DIR__ . '/../src/vendor/autoload.php';

use reunionou\backoffice\app\controller\BackOfficeController as BackOfficeController;

//Loading configs
$settings = require_once __DIR__. '/../src/app/conf/settings.php';
$app_config = array_merge($settings);

//Init Slim App
$app = new \Slim\App(new \Slim\Container($app_config));


// Set the differents routes

 


$app->run();