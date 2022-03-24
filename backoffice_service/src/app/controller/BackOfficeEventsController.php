<?php

namespace reunionou\backoffice\app\controller;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use GuzzleHttp\Client as Client;

use reunionou\backoffice\app\utils\Writer;

class BackOfficeEventsController
{

    private $container; // le conteneur de dépendences de l'application
    

    public function __construct(\Slim\Container $container)
    {
        $this->container = $container;
    }


    // public function authenticate(Request $req, Response $resp, $args): Response {

    //     $client = new Client([
    //         'base_uri' => $this->container->get('settings')['auth_service'],
    //         'timeout' => 5.0,
    //     ]);
    //     $response = $client->request('POST', '/auth', [
    //             'headers'=> ['Authorization' => $req->getHeader('Authorization')]

    //     ]);
    //     return $resp->withHeader('Content-Type', $response->getHeader('Content-Type'));
    // }


    public function getAllEvent(Request $req, Response $resp, $args): Response {

        $client = new \GuzzleHttp\Client([
            'base_uri' => $this->container ->get('settings')['events_service'],
            'timeout' => 5.0
        ]);

          if (!$req->hasHeader('Authorization')) {

            $resp = $resp->withHeader('WWW-authenticate', 'Basic realm="users_api api" ');
            return Writer::json_error($resp, 401, 'No Authorization header present');
        } else {
            $response = $client->request('POST', '/events', [
                'headers'=> ['Authorization' => $req->getHeader('Authorization')]
            ]);
            
        }


        $resp = Writer::json_output($resp, $response->getStatusCode());
        $resp->getBody()->write($response->getBody());
        return $resp;
    }


}