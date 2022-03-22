<?php

namespace reunionou\auth\app\controller;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException ;
use Firebase\JWT\BeforeValidException;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use lbs\auth\app\model\User;
use lbs\auth\app\error\Writer as Writer;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;


class AuthController {

    private $container; // le conteneur de dépendences de l'application
    

    public function __construct(\Slim\Container $container)
    {
        $this->container = $container;
    }

    public function hello(Request $req, Response $resp, $args): Response {

        // //configure the response headers
        $resp = $resp->withStatus(200)
            ->withHeader('Content-Type', 'application/json; charset=utf-8');


        //write in the body with data encode with a json_encode function
        $resp->getBody()->write('$data');

        return $resp;

    }
    
}
