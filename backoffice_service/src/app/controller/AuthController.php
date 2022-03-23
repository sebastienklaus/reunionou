<?php

namespace reunionou\backoffice\app\controller;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;


use Illuminate\Database\Eloquent\ModelNotFoundException;

// Custom classes
// use reunionou\auth\app\model\User as User;
// use reunionou\auth\app\error\Writer as Writer;

class BackOfficeController {

    private $container; // le conteneur de dépendences de l'application
    

    public function __construct(\Slim\Container $container)
    {
        $this->container = $container;
    }

    
}