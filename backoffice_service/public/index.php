<?php

require_once  __DIR__ . '/../src/vendor/autoload.php';

use reunionou\backoffice\app\controller\BackOfficeAuthController as BackOfficeAuthController;

//Loading configs
$settings = require_once __DIR__. '/../src/app/conf/settings.php';
$app_config = array_merge($settings);

//Init Slim App
$app = new \Slim\App(new \Slim\Container($app_config));

require_once __DIR__ . '/../src/app/routes/routesAuth.php';

require_once __DIR__ . '/../src/app/routes/routesEvents.php';




$app->run();