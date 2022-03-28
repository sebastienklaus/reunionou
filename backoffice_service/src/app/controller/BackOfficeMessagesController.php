<?php

namespace reunionou\backoffice\app\controller;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use reunionou\backoffice\app\utils\Writer;

use GuzzleHttp\Psr7;
use GuzzleHttp\Client as Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;

class BackOfficeMessagesController
{

    private $container; // le conteneur de dépendences de l'application
    

    public function __construct(\Slim\Container $container)
    {
        $this->container = $container;
    }

    public function getMessage(Request $req, Response $resp, $args): Response {

        try {
        $client = new \GuzzleHttp\Client([
            'base_uri' => $this->container->get('settings')['events_service'],
            'timeout' => 5.0
        ]);

        $id_message = $args['id'];
        $response = $client->request('GET', '/messages/' . $id_message);

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

    public function createMessage(Request $req, Response $resp, $args): Response {

        try {
        $client = new \GuzzleHttp\Client([
            'base_uri' => $this->container->get('settings')['events_service'],
            'timeout' => 5.0
        ]);

        $received_message = $req->getParsedBody();
        if(isset($received_message['media'])) {
            $response = $client->request('POST', '/messages', [
                    'form_params'=> [
                        'content' => $received_message['content'],
                        'member_id' => $received_message['member_id'],
                        'event_id' => $received_message['event_id'],
                        'media' => $received_message['media']
                    ]]  );
        } else {
            $response = $client->request('POST', '/messages', [
                    'form_params'=> [
                        'content' => $received_message['content'],
                        'member_id' => $received_message['member_id'],
                        'event_id' => $received_message['event_id']
                    ]]  );
        }} 
        catch (ClientException $e) { 
            $responseBodyAsString = $e->getResponse()->getBody()->getContents();
            return Writer::json_error_data($resp, 401, $responseBodyAsString);
        } 
        catch (ServerException $e) {
            $responseBodyAsString = $e->getResponse()->getBody()->getContents();
            return Writer::json_error_data($resp, 500, $responseBodyAsString);
        }  


        $resp = Writer::json_output($resp, $response->getStatusCode());
        
        $resp->getBody()->write($response->getBody());
        return $resp;

    }

    public function getMessagesByEvent(Request $req, Response $resp, $args): Response {

        try{
        $client = new \GuzzleHttp\Client([
            'base_uri' => $this->container->get('settings')['events_service'],
            'timeout' => 5.0
        ]);

        $id_event = $args['id'];
        $response = $client->request('GET', '/events/' . $id_event . '/messages/');

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

    public function deleteMessageById(Request $req, Response $resp, $args): Response {

        try{
        $client = new \GuzzleHttp\Client([
            'base_uri' => $this->container->get('settings')['events_service'],
            'timeout' => 5.0
        ]);

        $id_message = $args['id'];
        $response = $client->request('DELETE', '/messages/' . $id_message);

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