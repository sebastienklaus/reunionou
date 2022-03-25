<?php

namespace reunionou\backoffice\app\controller;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use GuzzleHttp\Client as Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use reunionou\backoffice\app\utils\Writer as Writer;

class BackOfficeAuthController
{

    private $container; // le conteneur de dépendences de l'application
    

    public function __construct(\Slim\Container $container)
    {
        $this->container = $container;
    }

    public function authenticate(Request $req, Response $resp, array $args): Response {


        try {
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
        } catch (ClientException $e) { 
            // return Writer::json_error($resp,400, $e->get());
        } catch (ServerException $e) {
            return Writer::json_error($resp,500, "$e->getResponse()");
        }        
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

    public function updateUser(Request $req, Response $resp, array $args): Response {
        $userID = $args['id'];
        $body = $req->getParsedBody();

        $client = new Client([
            'base_uri' => $this->container->get('settings')['auth_service'],
            'timeout' => 5.0,
        ]);

        $response = $client->request('PUT', '/update/'. $userID, [
                'form_params'=> [
                    'fullname' => $body['fullname'],
                    'email' => $body['email'],
                    'username' => $body['username'],
                    'old_password' => $body['old_password'],
                    'new_password' => $body['new_password'],
                    'new_password_confirm' => $body['new_password_confirm'],
                ]

        ]);
        return $resp->withStatus($response->getStatusCode())
                    ->withHeader('Content-Type', $response->getHeader('Content-Type'))
                    ->withBody($response->getBody());
    }

    public function getUsers(Request $req, Response $resp, array $args): Response {
        
        $client = new Client([
            'base_uri' => $this->container->get('settings')['auth_service'],
            'timeout' => 5.0,
        ]);
        $response = $client->request('GET', '/users');

        return $resp->withStatus($response->getStatusCode())
                    ->withHeader('Content-Type', $response->getHeader('Content-Type'))
                    ->withBody($response->getBody());
    }

    public function getUserById(Request $req, Response $resp, array $args): Response {
        $userID = $args['id'];

        $client = new Client([
            'base_uri' => $this->container->get('settings')['auth_service'],
            'timeout' => 5.0,
        ]);
        $response = $client->request('GET', '/users/'. $userID);

        return $resp->withStatus($response->getStatusCode())
                    ->withHeader('Content-Type', $response->getHeader('Content-Type'))
                    ->withBody($response->getBody());
    }

}