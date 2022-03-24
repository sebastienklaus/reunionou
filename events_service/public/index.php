<?php

/**
 * File:  index.php
 *
 */

require_once  __DIR__ . '/../src/vendor/autoload.php';

use reu\events\app\bootstrap\reunionouBootstrap;
use reu\events\app\middleware\Middleware as Middleware;

// video 3 contaeneur (de dépendance ?)
// $config = [
//     // 'dbconf' => __DIR__ . '/../src/conf/db.conf.ini',
//     'settings' => ['dbconf' => __DIR__ . '/../src/conf/db.conf.ini',
//                 'displayErrorDetails'=>true]
// ];

// => déplacé dans le settings.php dans conf

// ? Pourquoi pas un import ? Parceque pas une classe ?? Pk pas une classe ?
// ? parceque pas la peine ?
$config       = require_once __DIR__ . '/../src/app/conf/settings.php';
$dependencies = require_once __DIR__ . '/../src/app/conf/dependencies.php';
$errors       = require_once __DIR__ . '/../src/app/conf/errors.php';
$functions_test = require_once __DIR__ . '/../src/app/conf/functions_test.php';

//? Insérer le Writer pour pouvoir y logger depuis ?
// soit variable, soit arra_merge de plusieurs variable :
$container = new \Slim\Container(array_merge(
    $config,
    $dependencies,
    $errors,
    $functions_test
));

$app = new \Slim\App($container);

// $app->options('/{routes:.+}', function ($request, $response, $args) {
//     return $response;
// });

// $app->add(Middleware::class .':corsHeaders');

reunionouBootstrap::startEloquent($container->settings['dbconf']);
$container->get('logger.debug')->debug('eloquent started - routes register started');

require_once __DIR__ . '/../src/app/routes/routes.php';

// $app->map(['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], '/{routes:.+}', function($req, $res) {
//     $handler = $this->notFoundHandler; 
//     return $handler($req, $res);
// });


$app->run();
