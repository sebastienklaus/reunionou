<?php

namespace reunionou\backoffice\app\middleware;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use GuzzleHttp\Client as Client;
use reunionou\backoffice\app\utils\Writer as Writer;

class Middleware {

    private $container; // le conteneur de dépendences de l'application

    public function __construct(\Slim\Container $container)
    {
        $this->container = $container;
    }

    // * ccheck d'authorization pour chaque requête effectué
    public function checkAuth(Request $req, Response $resp, callable $next){  
        $client = new Client([
            'base_uri' => $this->container->get('settings')['auth_service'],
            'timeout' => 5.0
            ]);

        $response = $client->request('GET', '/checkAdmin', [
                'headers'=> ['Authorization' => $req->getHeader('Authorization')]
            ]
        );

        $body = json_decode($response->getBody());

        $req = $req->withAttribute('token', $req->getHeader('Authorization'));

        $resp = $next($req,$resp);

        return $resp;

    }

    // * ajout des headers CORS pour l'utilisation des PAI cross-domain
    public static function corsHeaders(Request $req,Response $resp,callable $next ): Response {
        if (! $req->hasHeader('Origin'))
            return Writer::json_error($resp, 401, "missing Origin Header (cors)");
        
        $response = $next($req,$resp);

        $response = $response->withHeader('Access-Control-Allow-Origin', $req->getHeader('Origin'))
                                ->withHeader('Access-Control-Allow-Methods', 'POST, PUT, GET, DELTE' )
                                ->withHeader('Access-Control-Allow-Headers','Authorization, Content-Type' )
                                ->withHeader('Access-Control-Max-Age', 3600)
                                ->withHeader('Access-Control-Allow-Credentials', 'true');
        return $response;
    }

    // * 
    // * ccheck d'authorization pour chaque requête effectué
    public function checkAdmin(Request $req, Response $resp, callable $next){  
        $client = new Client([
            'base_uri' => $this->container->get('settings')['auth_service'],
            'timeout' => 5.0
            ]);

        $response = $client->request('POST', '/check', [
                'headers'=> ['Authorization' => $req->getHeader('Authorization')]
            ]
        );

        $body = json_decode($response->getBody());
        var_dump($body);
        // $req = $req->withAttribute('token', $req->getHeader('Authorization'));

        $resp = $next($req,$resp);

        return $resp;

    }

}