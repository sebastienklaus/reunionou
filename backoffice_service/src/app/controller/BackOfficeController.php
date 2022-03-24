<?php

namespace reunionou\backoffice\app\controller;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use GuzzleHttp\Client as Client;

class BackOfficeController
{

    private $container; // le conteneur de dépendences de l'application
    

    public function __construct(\Slim\Container $container)
    {
        $this->container = $container;
    }


    public function authenticate(Request $req, Response $resp, $args): Response {


        try {
            $client = new Client([
                'base_uri' => $this->container->get('settings')['auth_service'],
                'timeout' => 5.0,
                ]);
            $response = $client->request('GET', '/users/2bcb5856-7614-4ae8-a531-513962950326');
            $json = json_decode($response->getBody()) ;
            echo $response->getBody();
       } catch (\GuzzleHttp\Exception\ConnectException $e) {
            // Couldn't connect, handle it how you need
            echo $response->getStatusCode();
       }
       
        return $resp;

    }



}