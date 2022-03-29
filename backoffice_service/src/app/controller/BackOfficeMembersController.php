<?php

namespace reunionou\backoffice\app\controller;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use reunionou\backoffice\app\utils\Writer;

use GuzzleHttp\Psr7;
use GuzzleHttp\Client as Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;

class BackOfficeMembersController
{

    private $container; // le conteneur de dépendences de l'application


    public function __construct(\Slim\Container $container)
    {
        $this->container = $container;
    }

    public function getMember(Request $req, Response $resp, $args): Response
    {

        try {
            $client = new \GuzzleHttp\Client([
                'base_uri' => $this->container->get('settings')['events_service'],
                'timeout' => 5.0
            ]);

            $id_member = $args['id'];
            $response = $client->request('GET', '/members/' . $id_member);

            $resp = Writer::json_output($resp, $response->getStatusCode());
            $resp->getBody()->write($response->getBody());
            return $resp;
        } catch (ClientException $e) {
            $responseBodyAsString = $e->getResponse()->getBody()->getContents();
            return Writer::json_error_data($resp, 401, $responseBodyAsString);
        } catch (ServerException $e) {
            $responseBodyAsString = $e->getResponse()->getBody()->getContents();
            return Writer::json_error_data($resp, 500, $responseBodyAsString);
        } catch (\Exception $e) {
            return Writer::json_error($resp, 500, "Server Error: can't getMember");
        }
    }

    public function createMember(Request $req, Response $resp, $args): Response
    {

        try {
            $client = new \GuzzleHttp\Client([
                'base_uri' => $this->container->get('settings')['events_service'],
                'timeout' => 5.0
            ]);

            $received_member = $req->getParsedBody();


            //* Génére automatiquement l'URI avec l'user_id vers la route getUserById
            // if (isset($received_member['user_id'])) {
            //         $pathForUser = $this->container->router->pathFor(
            //         'getUserById',
            //         ['id' => $received_member['user_id']]);
            //          } else {
            //         $pathForUser = null;
            //     };

            if (!isset($received_member['user_id'])) {
                $received_member['user_id'] = null;
            }

            if (!isset($received_member['status']) || ($received_member['status'] == "")) {
                $received_member['status'] = "-1";
            }

            $response = $client->request('POST', '/members', [
                'form_params' => [
                    'pseudo' => $received_member['pseudo'],
                    'event_id' => $received_member['event_id'],
                    'user_id' => $received_member['user_id'],
                    'status' => $received_member['status']
                ]
            ]);

            $resp = Writer::json_output($resp, $response->getStatusCode());

            $resp->getBody()->write($response->getBody());
            return $resp;
        } catch (ClientException $e) {
            $responseBodyAsString = $e->getResponse()->getBody()->getContents();
            return Writer::json_error_data($resp, 401, $responseBodyAsString);
        } catch (ServerException $e) {
            $responseBodyAsString = $e->getResponse()->getBody()->getContents();
            return Writer::json_error_data($resp, 500, $responseBodyAsString);
        } catch (\Exception $e) {
            return Writer::json_error($resp, 500, "Server Error: can't Create Member");
        }
    }

    public function updateMember(Request $req, Response $resp, $args): Response
    {

        try {
            $client = new \GuzzleHttp\Client([
                'base_uri' => $this->container->get('settings')['events_service'],
                'timeout' => 5.0
            ]);

            $id_event = $args['id'];
            $received_member = $req->getParsedBody();

            // if (isset($received_member['user_id'])) {
            //         $pathForUser = $this->container->router->pathFor(
            //         'getUserById',
            //         ['id' => $received_member['user_id']]);
            //          } else {
            //         $pathForUser = null;
            //     };


            $response = $client->request('PUT', '/members/' . $id_event, [
                'form_params' => [
                    'pseudo' => $received_member['pseudo'],
                    'event_id' => $received_member['event_id'],
                    'user_id' => $received_member['user_id'],
                    'status' => $received_member['status']
                ]
            ]);

            $resp = Writer::json_output($resp, $response->getStatusCode());
            $resp->getBody()->write($response->getBody());
            return $resp;
        } catch (ClientException $e) {
            $responseBodyAsString = $e->getResponse()->getBody()->getContents();
            return Writer::json_error_data($resp, 401, $responseBodyAsString);
        } catch (ServerException $e) {
            $responseBodyAsString = $e->getResponse()->getBody()->getContents();
            return Writer::json_error_data($resp, 500, $responseBodyAsString);
        } catch (\Exception $e) {
            return Writer::json_error($resp, 500, "Server Error: can't update member");
        }
    }

    public function getMembersByEvent(Request $req, Response $resp, $args): Response
    {

        try {
            $client = new \GuzzleHttp\Client([
                'base_uri' => $this->container->get('settings')['events_service'],
                'timeout' => 5.0
            ]);

            $id_event = $args['id'];
            $response = $client->request('GET', '/events/' . $id_event . '/members/');

            $resp = Writer::json_output($resp, $response->getStatusCode());
            $resp->getBody()->write($response->getBody());
            return $resp;
        } catch (ClientException $e) {
            $responseBodyAsString = $e->getResponse()->getBody()->getContents();
            return Writer::json_error_data($resp, 401, $responseBodyAsString);
        } catch (ServerException $e) {
            $responseBodyAsString = $e->getResponse()->getBody()->getContents();
            return Writer::json_error_data($resp, 500, $responseBodyAsString);
        } catch (\Exception $e) {
            return Writer::json_error($resp, 500, "Server Error: can't get member");
        }
    }

    public function deleteMemberById(Request $req, Response $resp, $args): Response
    {

        try {
            $client = new \GuzzleHttp\Client([
                'base_uri' => $this->container->get('settings')['events_service'],
                'timeout' => 5.0
            ]);

            $id_member = $args['id'];
            $response = $client->request('DELETE', '/members/' . $id_member);

            $resp = Writer::json_output($resp, $response->getStatusCode());
            $resp->getBody()->write($response->getBody());
            return $resp;
        } catch (ClientException $e) {
            $responseBodyAsString = $e->getResponse()->getBody()->getContents();
            return Writer::json_error_data($resp, 401, $responseBodyAsString);
        } catch (ServerException $e) {
            $responseBodyAsString = $e->getResponse()->getBody()->getContents();
            return Writer::json_error_data($resp, 500, $responseBodyAsString);
        } catch (\Exception $e) {
            return Writer::json_error($resp, 500, "Server Error: can't delete member");
        }
    }

    public function getMembersByUserId(Request $req, Response $resp, $args): Response
    {

        try {
            $client = new \GuzzleHttp\Client([
                'base_uri' => $this->container->get('settings')['events_service'],
                'timeout' => 5.0
            ]);

            $id_user = $args['id'];
            $response = $client->request('GET', '/users/' . $id_user . '/members/');

            $resp = Writer::json_output($resp, $response->getStatusCode());
            $resp->getBody()->write($response->getBody());
            return $resp;
        } catch (ClientException $e) {
            $responseBodyAsString = $e->getResponse()->getBody()->getContents();
            return Writer::json_error_data($resp, 401, $responseBodyAsString);
        } catch (ServerException $e) {
            $responseBodyAsString = $e->getResponse()->getBody()->getContents();
            return Writer::json_error_data($resp, 500, $responseBodyAsString);
        } catch (\Exception $e) {
            return Writer::json_error($resp, 500, "Server Error: can't get member");
        }
    }


    public function getOneMember(Request $req, Response $resp, $args): Response
    {
        try {
            $query = $req->getQueryParams();

            $client = new \GuzzleHttp\Client([
                'base_uri' => $this->container->get('settings')['events_service'],
                'timeout' => 5.0
            ]);

            $response = $client->request('GET', '/member/', [
                'query' => $query
            ]);

            $resp = Writer::json_output($resp, $response->getStatusCode());
            $resp->getBody()->write($response->getBody());
            return $resp;
        } catch (ClientException $e) {
            $responseBodyAsString = $e->getResponse()->getBody()->getContents();
            return Writer::json_error_data($resp, 401, $responseBodyAsString);
        } catch (ServerException $e) {
            $responseBodyAsString = $e->getResponse()->getBody()->getContents();
            return Writer::json_error_data($resp, 500, $responseBodyAsString);
        } catch (\Exception $e) {
            return Writer::json_error($resp, 500, "Server Error: can't get member");
        }
    }


    public function getMembers(Request $req, Response $resp, $args): Response
    {

        try {
            $client = new \GuzzleHttp\Client([
                'base_uri' => $this->container->get('settings')['events_service'],
                'timeout' => 5.0
            ]);

            $response = $client->request('GET', '/members/');

            $resp = Writer::json_output($resp, $response->getStatusCode());
            $resp->getBody()->write($response->getBody());
            return $resp;
        } catch (ClientException $e) {
            $responseBodyAsString = $e->getResponse()->getBody()->getContents();
            return Writer::json_error_data($resp, 401, $responseBodyAsString);
        } catch (ServerException $e) {
            $responseBodyAsString = $e->getResponse()->getBody()->getContents();
            return Writer::json_error_data($resp, 500, $responseBodyAsString);
        } catch (\Exception $e) {
            return Writer::json_error($resp, 500, "Server Error: can't get member");
        }
    }
}
