<?php

namespace reunionou\backoffice\app\controller;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use GuzzleHttp\Client as Client;

use reunionou\backoffice\app\utils\Writer;

use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;

class BackOfficeEventsController
{

    private $container; // le conteneur de dépendences de l'application
    

    public function __construct(\Slim\Container $container)
    {
        $this->container = $container;
    }


    public function getAllEvent(Request $req, Response $resp, $args): Response {

        try {
        $client = new \GuzzleHttp\Client([
            'base_uri' => $this->container->get('settings')['events_service'],
            'timeout' => 5.0
        ]);

        $response = $client->request('GET', '/events');

        $resp = Writer::json_output($resp, $response->getStatusCode());
        $resp->getBody()->write($response->getBody());
        return $resp;
    } 
    catch (ClientException $e) { 
        $responseBodyAsString = $e->getResponse()->getBody()->getContents();
        return Writer::json_error_data($resp, 401, $responseBodyAsString);
    } 
    catch (ServerException $e) {
        $responseBodyAsString = $e->getResponse()->getBody()->getContents();
        return Writer::json_error_data($resp, 500, $responseBodyAsString);
    }   

    }

    public function getEvent(Request $req, Response $resp, $args): Response {

        try {
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
    catch (ClientException $e) { 
        $responseBodyAsString = $e->getResponse()->getBody()->getContents();
        return Writer::json_error_data($resp, 401, $responseBodyAsString);
    } 
    catch (ServerException $e) {
        $responseBodyAsString = $e->getResponse()->getBody()->getContents();
        return Writer::json_error_data($resp, 500, $responseBodyAsString);
    }   

    }

    public function createEvent(Request $req, Response $resp, $args): Response {

        try {
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
                    'location' => json_encode($received_event['location']),
                    'date' => $received_event['date'],
                    'heure' => $received_event['heure']
                ]]  );

        $resp = Writer::json_output($resp, $response->getStatusCode());
        
        $resp->getBody()->write($response->getBody());
        return $resp;
    } 
    catch (ClientException $e) { 
        $responseBodyAsString = $e->getResponse()->getBody()->getContents();
        return Writer::json_error_data($resp, 401, $responseBodyAsString);
    } 
    catch (ServerException $e) {
        $responseBodyAsString = $e->getResponse()->getBody()->getContents();
        return Writer::json_error_data($resp, 500, $responseBodyAsString);
    }   

    }

    public function updateEvent(Request $req, Response $resp, $args): Response {

        try{
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
                'location' => json_encode($received_event['location']),
                'date' => $received_event['date'],
                'heure' => $received_event['heure']
                ]]  );

        $resp = Writer::json_output($resp, $response->getStatusCode());
        $resp->getBody()->write($response->getBody());
        return $resp;
    } 
    catch (ClientException $e) { 
        $responseBodyAsString = $e->getResponse()->getBody()->getContents();
        return Writer::json_error_data($resp, 401, $responseBodyAsString);
    } 
    catch (ServerException $e) {
        $responseBodyAsString = $e->getResponse()->getBody()->getContents();
        return Writer::json_error_data($resp, 500, $responseBodyAsString);
    }   

    }

    public function getEventByMemberPseudo(Request $req, Response $resp, $args): Response {

        try {
        $client = new \GuzzleHttp\Client([
            'base_uri' => $this->container->get('settings')['events_service'],
            'timeout' => 5.0
        ]);

        $pseudo = $args['pseudo'];
        $response = $client->request('GET', '/members/' . $pseudo . '/events/');

        $resp = Writer::json_output($resp, $response->getStatusCode());
        $resp->getBody()->write($response->getBody());
        return $resp;
    } 
    catch (ClientException $e) { 
        $responseBodyAsString = $e->getResponse()->getBody()->getContents();
        return Writer::json_error_data($resp, 401, $responseBodyAsString);
    } 
    catch (ServerException $e) {
        $responseBodyAsString = $e->getResponse()->getBody()->getContents();
        return Writer::json_error_data($resp, 500, $responseBodyAsString);
    }   

    }

    public function getEventCreatedByUserId(Request $req, Response $resp, $args): Response {

        try {
        $client = new \GuzzleHttp\Client([
            'base_uri' => $this->container->get('settings')['events_service'],
            'timeout' => 5.0
        ]);

        $user_id = $args['id'];
        $response = $client->request('GET', '/users/' . $user_id . '/created_events/');

        $resp = Writer::json_output($resp, $response->getStatusCode());
        $resp->getBody()->write($response->getBody());
        return $resp;
    } 
    catch (ClientException $e) { 
        $responseBodyAsString = $e->getResponse()->getBody()->getContents();
        return Writer::json_error_data($resp, 401, $responseBodyAsString);
    } 
    catch (ServerException $e) {
        $responseBodyAsString = $e->getResponse()->getBody()->getContents();
        return Writer::json_error_data($resp, 500, $responseBodyAsString);
    }   

    }

    public function deleteEventById(Request $req, Response $resp, $args): Response {


        try {
        $client = new \GuzzleHttp\Client([
            'base_uri' => $this->container->get('settings')['events_service'],
            'timeout' => 5.0
        ]);

        $id_event = $args['id'];
        $response = $client->request('DELETE', '/events/' . $id_event);

        $resp = Writer::json_output($resp, $response->getStatusCode());
        $resp->getBody()->write($response->getBody());
        return $resp;
    } 
    catch (ClientException $e) { 
        $responseBodyAsString = $e->getResponse()->getBody()->getContents();
        return Writer::json_error_data($resp, 401, $responseBodyAsString);
    } 
    catch (ServerException $e) {
        $responseBodyAsString = $e->getResponse()->getBody()->getContents();
        return Writer::json_error_data($resp, 500, $responseBodyAsString);
    }  

    }

    public function getAllEventsByMemberId(Request $req, Response $resp, $args): Response {

        try {
        $client = new \GuzzleHttp\Client([
            'base_uri' => $this->container->get('settings')['events_service'],
            'timeout' => 5.0
        ]);

        $member_id = $args['id'];
        $response = $client->request('GET', '/members_users/' . $member_id . '/events/');

        $resp = Writer::json_output($resp, $response->getStatusCode());
        $resp->getBody()->write($response->getBody());
        return $resp;
    } 
    catch (ClientException $e) { 
        $responseBodyAsString = $e->getResponse()->getBody()->getContents();
        return Writer::json_error_data($resp, 401, $responseBodyAsString);
    } 
    catch (ServerException $e) {
        $responseBodyAsString = $e->getResponse()->getBody()->getContents();
        return Writer::json_error_data($resp, 500, $responseBodyAsString);
    }   

    }

    public function getAllEventsByUserId(Request $req, Response $resp, $args): Response {

        try {
        $client = new \GuzzleHttp\Client([
            'base_uri' => $this->container->get('settings')['events_service'],
            'timeout' => 5.0
        ]);

        $member_id = $args['id'];
        $response = $client->request('GET', '/users/' . $member_id . '/events/');

        $resp = Writer::json_output($resp, $response->getStatusCode());
        $resp->getBody()->write($response->getBody());
        return $resp;
    } 
    catch (ClientException $e) { 
        $responseBodyAsString = $e->getResponse()->getBody()->getContents();
        return Writer::json_error_data($resp, 401, $responseBodyAsString);
    } 
    catch (ServerException $e) {
        $responseBodyAsString = $e->getResponse()->getBody()->getContents();
        return Writer::json_error_data($resp, 500, $responseBodyAsString);
    }   

    }

}