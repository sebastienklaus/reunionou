<?php

return [
    'settings' => [
        'displayErrorDetails' => true,
        
        //client(s) Guzzle
        'auth_service' => 'http://api.auth.local',
        'events_service' => 'http://api.backoffice.local',

        'debug.name' => 'lbs.log',
        'debug.log' => __DIR__ . '/../log/debug.log',
        'debug.level' => \Monolog\Logger::DEBUG, // tt les msg à partir du nv debug seront rec

        'warn.name' => 'lbs.log',
        'warn.log' => __DIR__ . '/../log/warn.log',
        'warn.level' => \Monolog\Logger::WARNING, // or WARN ??

        'error.name' => 'lbs.log',               //* Nom du log     
        'error.log' => __DIR__ . '/../log/error.log',  //* Nom du fichier du log    
        'error.level' => \Monolog\Logger::ERROR,       //* Niveau de base du log
    ],
];