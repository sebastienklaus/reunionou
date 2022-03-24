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
            'base_uri' => $this->container->get('settings')['events_service'],
            'timeout' => 5.0
        ]);

        $response = $client->request('GET', '/events');

        $resp = Writer::json_output($resp, $response->getStatusCode());
        $resp->getBody()->write($response->getBody());
        return $resp;

    }

    public function getEvent(Request $req, Response $resp, $args): Response {


        $client = new \GuzzleHttp\Client([
            'base_uri' => $this->container->get('settings')['events_service'],
            'timeout' => 5.0
        ]);

        $id_event = $args['id'];
        $response = $client->request('GET', '/events/' . $id_event);

        $resp = Writer::json_output($resp, $response->getStatusCode());
        $resp->getBody()->write($response->getBody());
        return $resp;

    }

    public function createEvent(Request $req, Response $resp, $args): Response {


        $client = new \GuzzleHttp\Client([
            'base_uri' => $this->container->get('settings')['events_service'],
            'timeout' => 5.0
        ]);

        $received_event = $req->getParsedBody();

        $response = $client->request('POST', '/events', [
                'form_params'=> [
                    'title' => $received_event['title'],
                    'description' => $received_event['description'],
                    'user_id' => $received_event['user_id'],
                    'location' => $received_event['location'],
                    'date' => $received_event['date'],
                    'heure' => $received_event['heure']
                ]]  );

        $resp = Writer::json_output($resp, $response->getStatusCode());
        
        $resp->getBody()->write($response->getBody());
        return $resp;

    }

        public function updateEvent(Request $req, Response $resp, $args): Response {


        $client = new \GuzzleHttp\Client([
            'base_uri' => $this->container->get('settings')['events_service'],
            'timeout' => 5.0
        ]);

        $id_event = $args['id'];
        $received_event = $req->getParsedBody();
        
        $response = $client->request('PUT', '/events/' . $id_event, [
            'form_params'=> [
                'title' => $received_event['title'],
                'description' => $received_event['description'],
                'user_id' => $received_event['user_id'],
                'location' => $received_event['location'],
                'date' => $received_event['date'],
                'heure' => $received_event['heure']
                ]]  );
                
        var_dump($response);

        $resp = Writer::json_output($resp, $response->getStatusCode());
        $resp->getBody()->write($response->getBody());
        return $resp;

    }



}