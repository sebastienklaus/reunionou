<?php

namespace reunionou\auth\app\middleware;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use reunionou\auth\app\error\Writer as Writer;

use Ramsey\Uuid\Uuid;
use \DavidePastore\Slim\Validation\Validation as Validation ;

class Middleware {

    private $container; // le conteneur de dépendences de l'application

    public function __construct(\Slim\Container $container)
    {
        $this->container = $container;
    }

    // * création d'un ID avec un UUID (utilisation de la librairie Ramsey\UUID)
    public static function createID(Request $req, Response $resp, callable $next){
        // création d'un ID avec la librairie UUID
        $uuid4_id = Uuid::uuid4();
        // creation de l'attribut "idCommande"
        $req = $req->withAttribute('idUser', $uuid4_id->toString());

        $resp = $next($req,$resp);

        return $resp;
    }

    public static function corsHeaders(Request $req,Response $resp,callable $next ): Response {
        // if (! $req->hasHeader('Origin'))
        //     return Writer::jsonError($req, $resp, 401, "missing Origin Header (cors)");
        
        $response = $next($req,$resp);

        $response = $response->withHeader('Access-Control-Allow-Origin', $req->getHeader('Origin'))
                                ->withHeader('Access-Control-Allow-Methods', 'POST, PUT, GET, DELTE' )
                                ->withHeader('Access-Control-Allow-Headers','Authorization, Content-Type' )
                                ->withHeader('Access-Control-Max-Age', 3600)
                                ->withHeader('Access-Control-Allow-Credentials', 'true');
        return $response;
    }

}