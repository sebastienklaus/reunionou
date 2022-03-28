<?php

namespace reunionou\backoffice\app\controller;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use GuzzleHttp\Client as Client;

use reunionou\backoffice\app\utils\Writer;

class BackOfficeMessagesController
{

    private $container; // le conteneur de dépendences de l'application
    

    public function __construct(\Slim\Container $container)
    {
        $this->container = $container;
    }

    public function getMessage(Request $req, Response $resp, $args): Response {


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

    public function createMessage(Request $req, Response $resp, $args): Response {


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
        }


        $resp = Writer::json_output($resp, $response->getStatusCode());
        
        $resp->getBody()->write($response->getBody());
        return $resp;

    }

    public function getMessagesByEvent(Request $req, Response $resp, $args): Response {


        $client = new \GuzzleHttp\Client([
            'base_uri' => $this->container->get('settings')['events_service'],
            'timeout' => 5.0
        ]);

        $id_event = $args['id'];
        $response = $client->request('GET', '/events/' . $id_event . '/messages/');

        $resp = Writer::json_output($resp, $response->getStatusCode());
        $resp->getBody()->write($response->getBody());
        return $resp;

        // TODO : Récupérer href des membres

    }

    public function deleteMessageById(Request $req, Response $resp, $args): Response {


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

}