<?php

namespace reunionou\backoffice\app\controller;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use GuzzleHttp\Client as Client;

use reunionou\backoffice\app\controller\BackOfficeAuthController;

use reunionou\backoffice\app\utils\Writer;

class BackOfficeMembersController
{

    private $container; // le conteneur de dépendences de l'application
    

    public function __construct(\Slim\Container $container)
    {
        $this->container = $container;
    }

    public function getMember(Request $req, Response $resp, $args): Response {


        $client = new \GuzzleHttp\Client([
            'base_uri' => $this->container->get('settings')['events_service'],
            'timeout' => 5.0
        ]);

        $id_member = $args['id'];
        $response = $client->request('GET', '/members/' . $id_member);

        $resp = Writer::json_output($resp, $response->getStatusCode());
        $resp->getBody()->write($response->getBody());
        return $resp;

    }

    public function createMember(Request $req, Response $resp, $args): Response {


        $client = new \GuzzleHttp\Client([
            'base_uri' => $this->container->get('settings')['events_service'],
            'timeout' => 5.0
        ]);

        $received_member = $req->getParsedBody();

        $response = $client->request('POST', '/members', [
                'form_params'=> [
                    'pseudo' => $received_member['pseudo'],
                    'event_id' => $received_member['event_id'],
                    'user_id' => $received_member['user_id']
                ]]  );

        $resp = Writer::json_output($resp, $response->getStatusCode());
        
        $resp->getBody()->write($response->getBody());
        return $resp;

    }

    public function updateMember(Request $req, Response $resp, $args): Response {


        $client = new \GuzzleHttp\Client([
            'base_uri' => $this->container->get('settings')['events_service'],
            'timeout' => 5.0
        ]);

        $id_event = $args['id'];
        $received_member = $req->getParsedBody();
        
        $response = $client->request('PUT', '/members/' . $id_event, [
            'form_params'=> [
                'pseudo' => $received_member['pseudo'],
                'event_id' => $received_member['event_id'],
                'user_id' => $received_member['user_id']
                ]]  );

        $resp = Writer::json_output($resp, $response->getStatusCode());
        $resp->getBody()->write($response->getBody());
        return $resp;

    }

    public function getMembersByEvent(Request $req, Response $resp, $args): Response {


        $client = new \GuzzleHttp\Client([
            'base_uri' => $this->container->get('settings')['events_service'],
            'timeout' => 5.0
        ]);

        $id_event = $args['id'];
        $response = $client->request('GET', '/events/' . $id_event . '/members/');

        $resp = Writer::json_output($resp, $response->getStatusCode());
        $resp->getBody()->write($response->getBody());
        return $resp;

        // TODO : Récupérer href des membres

    }

    public function deleteMemberById(Request $req, Response $resp, $args): Response {


        $client = new \GuzzleHttp\Client([
            'base_uri' => $this->container->get('settings')['events_service'],
            'timeout' => 5.0
        ]);

        $id_member = $args['id'];
        $response = $client->request('DELETE', '/members/' . $id_member);

        $resp = Writer::json_output($resp, $response->getStatusCode());
        $resp->getBody()->write($response->getBody());
        return $resp;

    }

    public function getMembersByUserId(Request $req, Response $resp, $args): Response {


        $client_events = new \GuzzleHttp\Client([
            'base_uri' => $this->container->get('settings')['events_service'],
            'timeout' => 5.0
        ]);

        $client_auth = new \GuzzleHttp\Client([
            'base_uri' => $this->container->get('settings')['auth_service'],
            'timeout' => 5.0
        ]);


        $id_user = $args['user_id'];
        $user_response = $client_auth->request('GET', '/users/'. $id_user);
        $event_response = $client_events->request('GET', '/users/' . $id_user . '/members/');
        $datas_resp = [
            "type" => $event_response->getBody()

        ];
        $event = json_decode($event_response->getBody()->getContents());
        var_dump($event->member);

        // $resp = Writer::json_output($resp, 200);
        
        // $response->getBody()->write(json_encode($datas_resp));

        // $resp = Writer::json_output($resp, $response->getStatusCode());
        $resp = Writer::json_output($resp, "200"); //! à changer avec try catch pour gérer les erreur des deux requetes
        // $resp->getBody()->write($response->getBody());
        $resp->getBody()->write(json_encode($datas_resp));
        return $resp;

        // TODO : Récupérer href des membres

    }

}