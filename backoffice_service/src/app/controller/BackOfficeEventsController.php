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
     * @api {GET} /events Get All Event
     * @apiName GetAllEvent
     * @apiGroup Event
     * @apiVersion  1.0.0
     * 
     * @apiDescription Récupérer tous les événements
     * 
     * @apiSuccess (Success (200)) {String} title Titre de l'event
     * @apiSuccess (Success (200)) {String} description Description de l'event
     * @apiSuccess (Success (200)) {String} user_id ID de l'utilisateur ayant créé l'event
     * @apiSuccess (Success (200)) {JSON} location Lieu de l'event (nom, latitude et longitude)
     * @apiSuccess (Success (200)) {date} date Date de l'event
     * @apiSuccess (Success (200)) {date} heure Heure de l'event
     * @apiSuccess (Success (200)) {date} created_at Date de création de l'event
     * @apiSuccess (Success (200)) {date} updated_at Date de la dernière modification de l'event
     * @apiSuccess (Success (200)) {String} href Lien vers la ressource event
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

        /**
     * 
     * @api {GET} /events/{id} Get Event
     * @apiName GetEventById
     * @apiGroup Event
     * @apiVersion  1.0.0
     * 
     * @apiDescription Récupérer un événement.
     * 
     * @apiParam  {id} id id de l'évènement
     * 
     * @apiSuccess (Success (200)) {String} title Titre de l'event
     * @apiSuccess (Success (200)) {String} description Description de l'event
     * @apiSuccess (Success (200)) {String} user_id ID de l'utilisateur ayant créé l'event
     * @apiSuccess (Success (200)) {JSON} location Lieu de l'event (nom, latitude et longitude)
     * @apiSuccess (Success (200)) {date} date Date de l'event
     * @apiSuccess (Success (200)) {date} heure Heure de l'event
     * @apiSuccess (Success (200)) {date} created_at Date de création de l'event
     * @apiSuccess (Success (200)) {date} updated_at Date de la dernière modification de l'event
     * 
     * @apiSuccessExample Success-Response:
     *{
     * "type": "ressource",
     * "event": {
     *     "id": "0447ff47-e257-4bfc-b1a6-913a2c6cbd79",
     *     "title": "Princess Ka'iulani",
     *     "description": "In sagittis dui vel nisl. Duis ac nibh. Fusce lacus purus, aliquet at, feugiat non, pretium quis, lectus.",
     *     "user_id": "adcac5ad-206a-449c-8f40-aacb32750c47",
     *     "location": {
     *         "name": "Zoombox",
     *         "latitude": 117.9483319,
     *         "longitude": -8.3501717
     *     },
     *     "date": "2021-07-02",
     *     "heure": "12:35:00",
     *     "created_at": "2021-09-07 11:18:26",
     *     "updated_at": "2021-05-13 10:26:33"
     * },
     * "links": {
     *     "self": {
     *         "href": "/events/0447ff47-e257-4bfc-b1a6-913a2c6cbd79/"
     *     },
     *     "members": {
     *         "href": "/events/0447ff47-e257-4bfc-b1a6-913a2c6cbd79/members/"
     *     },
     *     "messages": {
     *         "href": "/events/0447ff47-e257-4bfc-b1a6-913a2c6cbd79/messages/"
     *     }
     * }
     *}
     */
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

        /**
     * 
     * @api {POST} /events Create Event
     * @apiName CreateEvent
     * @apiGroup Event
     * @apiVersion  1.0.0
     * 
     * @apiDescription Créer un événement.
     * 
     * @apiBody  {String} title Titre de l'event
     * @apiBody  {String} description Description de l'event
     * @apiBody  {String} user_id ID de l'utilisateur ayant créé l'event
     * @apiBody  {JSON} location Lieu de l'event
     * @apiBody  {date} date Date de l'event
     * @apiBody  {date} heure Heure de l'event
     *
     * @apiParamExample Request-Example:
     *{
     *"title" : "Event_Name",
     *"description" : "Event_Description",
     *"user_id" : "/users/User_Id",
     *"location" : {
     *    "name":"Location_Name",
     *    "latitude":00.00,
     *    "longitude":00.00
     *    },
     *"date" : "2000-01-01",
     *"heure" : "12:00"
     *}
     *  
     * @apiSuccess (Success (201)) {String} title Titre de l'event
     * @apiSuccess (Success (201)) {String} description Description de l'event
     * @apiSuccess (Success (201)) {String} user_id ID de l'utilisateur ayant créé l'event
     * @apiSuccess (Success (201)) {JSON} location Lieu de l'event (nom, latitude et longitude)
     * @apiSuccess (Success (201)) {date} date Date de l'event
     * @apiSuccess (Success (201)) {date} heure Heure de l'event
     * @apiSuccess (Success (201)) {date} created_at Date de création de l'event
     * @apiSuccess (Success (201)) {date} updated_at Date de la dernière modification de l'event
     * 
     * @apiSuccessExample Success-Response:
     *{
     *"type": "ressource",
     *"event": {
     *    "id": "Event_Id",
     *    "title": "Event_Name",
     *    "description": "Event_Description",
     *    "user_id": "/users/User_Id",
     *    "location" : {
     *       "name":"Location_Name",
     *       "latitude":00.00,
     *       "longitude":00.00
     *       },
     *    "date": "2000-01-01",
     *    "heure": "12:00:00",
     *    "created_at": "Creation_Date",
     *    "updated_at": "Creation_Date"
     *    }
     *}
     */
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

        /**
     * 
     * @api {PUT} /events/{id} Update Event
     * @apiName UpdateEvent
     * @apiGroup Event
     * @apiVersion  1.0.0
     * 
     * @apiDescription Mettre à jour un évenement.
     * 
     * @apiParam  {id} id id de l'évènement
     * 
     * @apiBody  {String} title Titre de l'event
     * @apiBody  {String} description Description de l'event
     * @apiBody  {String} user_id ID de l'utilisateur ayant créé l'event
     * @apiBody  {JSON} location Lieu de l'event
     * @apiBody  {date} date Date de l'event
     * @apiBody  {date} heure Heure de l'event
     * 
     * @apiParamExample Request-Example:
     *{
     *"title" : "Event_Name",
     *"description" : "Event_Description",
     *"user_id" : "/users/User_Id",
     *"location" : {
     *    "name":"Location_Name",
     *    "latitude":00.00,
     *    "longitude":00.00
     *    },
     *"date" : "2000-01-01",
     *"heure" : "12:00"
     *}
     * 
     * @apiSuccess (Success (200)) {String} title Titre de l'event
     * @apiSuccess (Success (200)) {String} description Description de l'event
     * @apiSuccess (Success (200)) {String} user_id ID de l'utilisateur ayant créé l'event
     * @apiSuccess (Success (200)) {JSON} location Lieu de l'event (nom, latitude et longitude)
     * @apiSuccess (Success (200)) {date} date Date de l'event
     * @apiSuccess (Success (200)) {date} heure Heure de l'event
     * @apiSuccess (Success (200)) {date} created_at Date de création de l'event
     * @apiSuccess (Success (200)) {date} updated_at Date de la dernière modification de l'event
     * 
     * @apiSuccessExample Success-Response:
     *{
     *"type": "ressource",
     *"event": {
     *    "id": "Event_Id",
     *    "title": "Event_Name",
     *    "description": "Event_Description",
     *    "user_id": "/users/User_Id",
     *    "location" : {
     *        "name":"Location_Name",
     *        "latitude":00.00,
     *        "longitude":00.00
     *        },,
     *    "date": "2000-01-01",
     *    "heure": "12:00:00",
     *    "created_at": "Creation_Date",
     *    "updated_at": "Modification_Date"
     *    }
     *}
     */
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

        /**
     * 
     * @api {GET} /members/{pseudo}/events Get Event by Member's Pseudo
     * @apiName GetEventByMemberPseudo
     * @apiGroup Event
     * @apiVersion  1.0.0
     * 
     * @apiDescription Récupérer un événement par le pseudo d'un membre
     * 
     * @apiParam  {pseudo} pseudo Pseudo du membre
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
     *"type": "ressource",
     *"event": {
     *    "id": "Event_Id",
     *    "title": "Event_Name",
     *    "description": "Event_Description",
     *    "user_id": "/users/User_Id",
     *    "location" : {
     *        "name":"Location_Name",
     *        "latitude":00.00,
     *        "longitude":00.00
     *        },,
     *    "date": "2000-01-01",
     *    "heure": "12:00:00",
     *    "created_at": "Creation_Date",
     *    "updated_at": "Modification_Date"
     *    }
     *}
     */
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


    /**
     * 
     * @api {GET} /users/{id}/created_events Get Events Created by User ID
     * @apiName GetEventCreatedByUserId
     * @apiGroup Event
     * @apiVersion  1.0.0
     * 
     * @apiDescription Récupérer les évenements dont l'utilisateur est le créateur
     * 
     * @apiParam  {id} id ID de l'utilisateur
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
     *"type": "ressource",
     *"event": {
     *    "id": "Event_Id",
     *    "title": "Event_Name",
     *    "description": "Event_Description",
     *    "user_id": "/users/User_Id",
     *    "location" : {
     *        "name":"Location_Name",
     *        "latitude":00.00,
     *        "longitude":00.00
     *        },,
     *    "date": "2000-01-01",
     *    "heure": "12:00:00",
     *    "created_at": "Creation_Date",
     *    "updated_at": "Modification_Date"
     *    }
     *}
     */
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

         /**
     * 
     * @api {DELETE} /events/{id} Delete an Event
     * @apiName DeleteEventById
     * @apiGroup Event
     * @apiVersion  1.0.0
     * 
     * @apiDescription Supprimer un évenement.
     * 
     * @apiParam  {String} id ID de l'évenement
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
     *  {
     * "type": "event",
     * "event": {
     *    "id": "Event_Id",
     *    "title": "Event_Name",
     *    "description": "Event_Description",
     *    "user_id": "/users/User_Id",
     *    "location" : {
     *        "name":"Location_Name",
     *        "latitude":00.00,
     *        "longitude":00.00
     *        },,
     *    "date": "2000-01-01",
     *    "heure": "12:00:00",
     *    "created_at": "Creation_Date",
     *    "updated_at": "Modification_Date"
     *    }
     * },
     * "response": "event deleted"
     * }
     */
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

         /**
     * 
     * @api {GET} /members_users/{id}/events Get All Event by Members ID
     * @apiName GetAllEventByMemberID
     * @apiGroup Event
     * @apiVersion  1.0.0
     * 
     * @apiParam  {String} id ID d'un membre
     * 
     * @apiDescription Récupérer tous l'évenement auquel un membre participe s'il est invité, ou tous les événements auxquels le compte user associé participe.
     * 
     * @apiSuccess (Success (200)) {String} title Titre de l'event
     * @apiSuccess (Success (200)) {String} description Description de l'event
     * @apiSuccess (Success (200)) {String} user_id ID de l'utilisateur ayant créé l'event
     * @apiSuccess (Success (200)) {JSON} location Lieu de l'event (nom, latitude et longitude)
     * @apiSuccess (Success (200)) {date} date Date de l'event
     * @apiSuccess (Success (200)) {date} heure Heure de l'event
     * @apiSuccess (Success (200)) {date} created_at Date de création de l'event
     * @apiSuccess (Success (200)) {date} updated_at Date de la dernière modification de l'event
     * @apiSuccess (Success (200)) {String} href Lien vers la ressource event
     * 
     * @apiSuccessExample Success-Response:
     *{
     * "type": "collection",
     * "count": 8,
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


         /**
     * 
     * @api {GET} /events Get All Event by User ID
     * @apiName GetAllEvent
     * @apiGroup Event
     * @apiVersion  1.0.0
     * 
     * @apiDescription Récupérer tous l'évenement auquel un utilisateur enregistré participe.
     * 
     * @apiSuccess (Success (200)) {String} title Titre de l'event
     * @apiSuccess (Success (200)) {String} description Description de l'event
     * @apiSuccess (Success (200)) {String} user_id ID de l'utilisateur ayant créé l'event
     * @apiSuccess (Success (200)) {JSON} location Lieu de l'event (nom, latitude et longitude)
     * @apiSuccess (Success (200)) {date} date Date de l'event
     * @apiSuccess (Success (200)) {date} heure Heure de l'event
     * @apiSuccess (Success (200)) {date} created_at Date de création de l'event
     * @apiSuccess (Success (200)) {date} updated_at Date de la dernière modification de l'event
     * @apiSuccess (Success (200)) {String} href Lien vers la ressource event
     * 
     * @apiSuccessExample Success-Response:
     *{
     * "type": "collection",
     * "count": 8,
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