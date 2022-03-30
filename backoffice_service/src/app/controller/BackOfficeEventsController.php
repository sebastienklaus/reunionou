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


     /**
     * 
     * @api {GET} /events getAllEvent
     * @apiName GetAllEvent
     * @apiGroup Event
     * @apiVersion  1.0.0
     * 
     * @apiSuccess (Success (200)) {String} title Titre de l'event
     * @apiSuccess (Success (200)) {String} description Description de l'event
     * @apiSuccess (Success (200)) {String} user_id ID de l'utilisateur ayant créé l'event
     * @apiSuccess (Success (200)) {JSON} location Lieu de l'event
     * @apiSuccess (Success (200)) {date} date Date de l'event
     * @apiSuccess (Success (200)) {date} heure Heure de l'event
     * @apiSuccess (Success (200)) {date} created_at Date de création de l'event
     * @apiSuccess (Success (200)) {date} updated_at Date de la dernière modification de l'event
     * 
     * @apiSuccessExample Success-Response:
     *{
     * "type": "collection",
     * "count": 24,
     * "events": [
     *     {
     *         "id": "0447ff47-e257-4bfc-b1a6-913a2c6cbd79",
     *         "title": "Princess Ka'iulani",
     *         "description": "In sagittis dui vel nisl. Duis ac nibh. Fusce lacus purus, aliquet at, feugiat non, pretium quis, lectus.",
     *         "user_id": "adcac5ad-206a-449c-8f40-aacb32750c47",
     *         "location": {
     *             "name": "Zoombox",
     *             "latitude": 117.9483319,
     *             "longitude": -8.3501717
     *         },
     *         "date": "2021-07-02",
     *         "heure": "12:35:00",
     *         "created_at": "2021-09-07 11:18:26",
     *         "updated_at": "2021-05-13 10:26:33",
     *         "href": "/events/0447ff47-e257-4bfc-b1a6-913a2c6cbd79/"
     *     },
     *     {
     *         "id": "0e37cc70-1961-452a-bb0f-84585d6c12dc",
     *         "title": "Two English Girls (Les deux anglaises et le continent)",
     * ...
     */
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

} // test de transfert