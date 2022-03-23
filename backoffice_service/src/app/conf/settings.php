<?php

return [
    'settings' => [
        'displayErrorDetails' => true,
        'tmpl_dir' => 'templates',
        
        //client(s) Guzzle
        'auth_service' => 'http://localhost',
    ],
    'view' => function ($c) {
        return new \Slim\Views\Twig(
            $c['settings']['tmpl_dir'],
            [
                'debug' => true,
                'cache' => $c['settings']['tmpl_dir']
            ]
        );
    }
];