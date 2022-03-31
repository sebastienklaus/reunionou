<?php

namespace reunionou\backoffice\app\controller;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use reunionou\backoffice\app\utils\Writer as Writer;

use GuzzleHttp\Psr7;
use GuzzleHttp\Client as Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;

class BackOfficeAuthController
{

    private $container; // le conteneur de dépendences de l'application


    public function __construct(\Slim\Container $container)
    {
        $this->container = $container;
    }

    public function authenticate(Request $req, Response $resp, array $args): Response
    {


        try {
            $client = new Client([
                'base_uri' => $this->container->get('settings')['auth_service'],
                'timeout' => 5.0,
            ]);
            $response = $client->request('POST', '/auth', [
                'headers' => ['Authorization' => $req->getHeader('Authorization')]

            ]);
            return $resp->withStatus($response->getStatusCode())
                ->withHeader('Content-Type', $response->getHeader('Content-Type'))
                ->withBody($response->getBody());
        } catch (ClientException $e) {
            $responseBodyAsString = $e->getResponse()->getBody()->getContents();
            return Writer::json_error_data($resp, 401, $responseBodyAsString);
        } catch (ServerException $e) {
            $responseBodyAsString = $e->getResponse()->getBody()->getContents();
            return Writer::json_error_data($resp, 500, $responseBodyAsString);
        }
    }


    /**
     * 
     * @api {POST} /users Create User
     * @apiName CreateUser
     * @apiGroup User
     * @apiVersion  1.0.0
     * 
     * @apiDescription Créer un utilisateur
     * 
     * @apiBody  {String} fullname Nom complet de l'utilisateur
     * @apiBody  {String} email Email de l'utilisateur
     * @apiBody  {String} username Username/pseudo de l'utilisateur
     * @apiBody  {String} password Mot de passe de l'utilisateur
     * @apiBody  {String} password_confirm Confirmation du mot de passe
     *
     * @apiParamExample Request-Example:
     *{
     *   "fullname": "Jean Dupond",
     *   "email": "jd@mail.net",
     *   "username": "jj"
     *   "new_passord": "strongpwd"
     *   "password_confirm": "strongpwd"
     *}
     * 
     * @apiSuccess (Success (201)) user_id UUID de l'utilisateur
     *
     */
    public function createUser(Request $req, Response $resp, $args): Response
    {
        $body = $req->getParsedBody();

        try {
            $client = new Client([
                'base_uri' => $this->container->get('settings')['auth_service'],
                'timeout' => 5.0,
            ]);

            $response = $client->request('POST', '/create', [
                'form_params' => [
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
        } catch (ClientException $e) {
            $responseBodyAsString = $e->getResponse()->getBody()->getContents();
            return Writer::json_error_data($resp, 401, $responseBodyAsString);
        } catch (ServerException $e) {
            $responseBodyAsString = $e->getResponse()->getBody()->getContents();
            return Writer::json_error_data($resp, 500, $responseBodyAsString);
        }
    }

        /**
     * 
     * @api {PUT} /users Update User
     * @apiName UpdateUser
     * @apiGroup User
     * @apiVersion  1.0.0
     * 
     * @apiDescription Modifier un utilisateur
     * 
     * @apiBody  {String} fullname Nom complet de l'utilisateur
     * @apiBody  {String} email Email de l'utilisateur
     * @apiBody  {String} username Username/pseudo de l'utilisateur
     * @apiBody  {String} password Mot de passe de l'utilisateur
     * @apiBody  {String} password_confirm Confirmation du mot de passe
     *
     * @apiParamExample Request-Example:
     *{
     *   "fullname": "Jean Dupond",
     *   "email": "jd@mail.net",
     *   "username": "jj"
     *   "passord": "strongpwd"
     *   "password_confirm": "strongpwd"
     *}
     * 
     * @apiSuccess (Success (200)) user_id UUID de l'utilisateur
     *
     */
    public function updateUser(Request $req, Response $resp, array $args): Response
    {
        $userID = $args['id'];
        $body = $req->getParsedBody();

        try {
            $client = new Client([
                'base_uri' => $this->container->get('settings')['auth_service'],
                'timeout' => 5.0,
            ]);

            $response = $client->request('PUT', '/update/' . $userID, [
                'form_params' => [
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
        } catch (ClientException $e) {
            $responseBodyAsString = $e->getResponse()->getBody()->getContents();
            return Writer::json_error_data($resp, 401, $responseBodyAsString);
        } catch (ServerException $e) {
            $responseBodyAsString = $e->getResponse()->getBody()->getContents();
            return Writer::json_error_data($resp, 500, $responseBodyAsString);
        }
    }

     /**
     * 
     * @api {GET} /users Get All Users
     * @apiName GetAllUser
     * @apiGroup User
     * @apiVersion  1.0.0
     * 
     * @apiDescription Récupérer tous les utilisateurs
     * 
     * @apiSuccess (Success (200)) {String} user_id UUID de l'utilisateur
     * @apiSuccess (Success (200)) {String} user_fullname Nom complet de l'utilisateur
     * @apiSuccess (Success (200)) {String} user_username Username ou pseudo de l'utilisateur
     * 
     * @apiSuccessExample Success-Response:
     *{
     * "type": "collection",
     * "count": 109,
     * "users": [
     *     {
     *         "user_id": "05edf56d-9436-45e9-8e23-6c9aad24ed07",
     *         "user_fullname": "Lyn McWaters",
     *         "user_username": "lmcwatersg"
     *     },
     *     {
     *         "user_id": "0c8116da-d8d4-4b09-9ebc-7f4785f5d4e6",
     *         "user_fullname": "Marin Conan",
     * ...
     */
    public function getUsers(Request $req, Response $resp, array $args): Response
    {
        try {
            $client = new Client([
                'base_uri' => $this->container->get('settings')['auth_service'],
                'timeout' => 5.0,
            ]);
            $response = $client->request('GET', '/users');

            return $resp->withStatus($response->getStatusCode())
                ->withHeader('Content-Type', $response->getHeader('Content-Type'))
                ->withBody($response->getBody());
        } catch (ClientException $e) {
            $responseBodyAsString = $e->getResponse()->getBody()->getContents();
            return Writer::json_error_data($resp, 401, $responseBodyAsString);
        } catch (ServerException $e) {
            $responseBodyAsString = $e->getResponse()->getBody()->getContents();
            return Writer::json_error_data($resp, 500, $responseBodyAsString);
        }
    }

    public function getUserById(Request $req, Response $resp, array $args): Response
    {
        $userID = $args['id'];

        try {
            $client = new Client([
                'base_uri' => $this->container->get('settings')['auth_service'],
                'timeout' => 5.0,
            ]);
            $response = $client->request('GET', '/users/' . $userID);

            return $resp->withStatus($response->getStatusCode())
                ->withHeader('Content-Type', $response->getHeader('Content-Type'))
                ->withBody($response->getBody());
        } catch (ClientException $e) {
            $responseBodyAsString = $e->getResponse()->getBody()->getContents();
            return Writer::json_error_data($resp, 401, $responseBodyAsString);
        } catch (ServerException $e) {
            $responseBodyAsString = $e->getResponse()->getBody()->getContents();
            return Writer::json_error_data($resp, 500, $responseBodyAsString);
        }
    }

    public function authenticateAdmin(Request $req, Response $resp, array $args): Response
    {
        try {
            $client = new Client([
                'base_uri' => $this->container->get('settings')['auth_service'],
                'timeout' => 5.0,
            ]);
            $response = $client->request('POST', '/auth', [
                'headers' => ['Authorization' => $req->getHeader('Authorization')]

            ]);
            return $resp->withStatus($response->getStatusCode())
                ->withHeader('Content-Type', $response->getHeader('Content-Type'))
                ->withBody($response->getBody());
        } catch (ClientException $e) {
            $responseBodyAsString = $e->getResponse()->getBody()->getContents();
            return Writer::json_error_data($resp, 401, $responseBodyAsString);
        } catch (ServerException $e) {
            $responseBodyAsString = $e->getResponse()->getBody()->getContents();
            return Writer::json_error_data($resp, 500, $responseBodyAsString);
        }
    }
}
