<?php

return [
    'settings' => [
        'displayErrorDetails' => true,
        'dbfile' => parse_ini_file('users.db.conf.ini'),
        'secret' => 'rsEEKo8wH2b4nNs6yTsxZWx90mI8sw22CBSrzS8b',

        //DEBUG LOG
        // 'debug.name' => 'slim.log',
        // 'debug.log' => __DIR__ . '/../log/debug.log',
        // 'debug.level' => \Monolog\Logger::DEBUG,
        
        //WARNING LOG
        // 'warning.name' => 'slim.log',
        // 'warning.log' => __DIR__ . '/../log/warning.log',
        // 'warning.level' => \Monolog\Logger::WARNING,
    ]
];