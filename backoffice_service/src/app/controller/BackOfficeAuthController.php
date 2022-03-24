<?php

namespace reunionou\backoffice\app\controller;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use GuzzleHttp\Client as Client;

class BackOfficeAuthController
{

    private $container; // le conteneur de dépendences de l'application
    

    public function __construct(\Slim\Container $container)
    {
        $this->container = $container;
    }


    public function authenticate(Request $req, Response $resp, $args): Response {

        $client = new Client([
            'base_uri' => $this->container->get('settings')['auth_service'],
            'timeout' => 5.0,
        ]);
        $response = $client->request('POST', '/auth', [
                'headers'=> ['Authorization' => $req->getHeader('Authorization')]

        ]);
        return $resp->withStatus($response->getStatusCode())
                    ->withHeader('Content-Type', $response->getHeader('Content-Type'))
                    ->withBody($response->getBody());
    }


    public function createUser(Request $req, Response $resp, $args): Response {

        $body = $req->getParsedBody();

        $client = new Client([
            'base_uri' => $this->container->get('settings')['auth_service'],
            'timeout' => 5.0,
        ]);

        $response = $client->request('POST', '/create', [
                'form_params'=> [
                    'fullname' => $body['fullname'],
                    'email' => $body['email'],
                    'username' => $body['username'],
                    'password' => $body['password'],
                    'password_confirm' => $body['password_confirm'],
                ]

        ]);
        return $resp->withStatus($response->getStatusCode())
                    ->withHeader('Content-Type', $response->getHeader('Content-Type'))
                    ->withBody($response->getBody());
    }




}