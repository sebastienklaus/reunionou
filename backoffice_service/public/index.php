<?php


require_once  __DIR__ . '/../src/vendor/autoload.php';

use reunionou\backoffice\app\middleware\Middleware as Middleware;


//Loading configs

$config       = require_once __DIR__ . '/../src/app/conf/settings.php';
$dependencies = require_once __DIR__ . '/../src/app/conf/dependencies.php';
$errors       = require_once __DIR__ . '/../src/app/conf/errors.php';

$app_config = array_merge(
    $config,
    $dependencies,
    $errors
);


//Init Slim App
$app = new \Slim\App(new \Slim\Container($app_config));

// routes to resolve CORS headers issue
$app->options('/{routes:.+}', function ($request, $response, $args) {
    return $response;
});

$app->add(Middleware::class .':corsHeaders');


require_once __DIR__ . '/../src/app/routes/routesAuth.php';

require_once __DIR__ . '/../src/app/routes/routesEvents.php';

require_once __DIR__ . '/../src/app/routes/routesMembers.php';

require_once __DIR__ . '/../src/app/routes/routesMessages.php';

// $app->map(['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], '/{routes:.+}', function($req, $res) {
//     $handler = $this->notFoundHandler; 
//     return $handler($req, $res);
// });

$app->run();
