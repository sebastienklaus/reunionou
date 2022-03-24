<?php

namespace  reu\events\app\middleware;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use GuzzleHttp\Client as Client;
use reu\events\app\utils\Writer as Writer;

class Middleware {

    private $container; // le conteneur de dépendences de l'application

    public function __construct(\Slim\Container $container)
    {
        $this->container = $container;
    }

    public static function corsHeaders(Request $req,Response $resp,callable $next ): Response {
        // if (! $req->hasHeader('Origin'))
        //     return Writer::json_error($resp, 401, "missing Origin Header (cors)");
        
        $response = $next($req,$resp);

        $response = $response->withHeader('Access-Control-Allow-Origin', $req->getHeader('Origin'))
                                ->withHeader('Access-Control-Allow-Methods', 'POST, PUT, GET, DELTE' )
                                ->withHeader('Access-Control-Allow-Headers','Authorization, Content-Type' )
                                ->withHeader('Access-Control-Max-Age', 3600)
                                ->withHeader('Access-Control-Allow-Credentials', 'true');
        return $response;
    }

}