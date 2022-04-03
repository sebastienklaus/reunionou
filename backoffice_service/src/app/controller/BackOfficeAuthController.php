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

    /**
     * 
     * @api {POST} /auth Authenticate
     * @apiName Authenticate
     * @apiGroup User
     * @apiVersion  1.0.0
     * 
     * @apiDescription Authentification
     * 
     * @apiHeader Basic_Auth Email de l'utilisateur
     *                     <br> Mot de passe de l'utilisateur
     * 
     * @apiHeaderExample {json} Header-Example:
     * {
     *   "Authorization": "Basic bWF4QG1heC5tYXg6bWF4(...)"
     * }
     *
     * @apiSuccess (Success (200)) {String} resfresh-token Refresh Token lié à l'utisateur
     * @apiSuccessExample Success-Response:
     *{
     * "refresh-token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vZG9ja2V0dS5pdXRuYy51b(...)"
     * }
     */
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

    /**
     * 
     * @api {GET} /users/{id} Get User By Id
     * @apiName GetUserById
     * @apiGroup User
     * @apiVersion  1.0.0
     * 
     * @apiDescription Récupérer un utilisateur par son ID
     * 
     * @apiParam  {id} id ID de l'user
     * 
     * @apiSuccess (Success (200)) {String} id UUID de l'utilisateur
     * @apiSuccess (Success (200)) {String} fullname Nom complet de l'utilisateur
     * @apiSuccess (Success (200)) {String} email Email Email de l'utilisateur
     * @apiSuccess (Success (200)) {String} user_username Username ou pseudo de l'utilisateur
     * @apiSuccess (Success (200)) {String} refesh_token Refresh_token ou pseudo de l'utilisateur
     * 
     * @apiSuccessExample Success-Response:
     *{
     * "type": "ressouce",
     * "user": {
     *     "id": "05edf56d-9436-45e9-8e23-6c9aad24ed07",
     *     "fullname": "Lyn McWaters",
     *     "email": "lmcwatersg@creativecommons.org",
     *     "username": "lmcwatersg",
     *     "refresh_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vZG9ja2V0dS5pdXRuYy51bml2LWxvcnJhaW5lLmZyOjYyMDExL2F1dGgiLCJhdWQiOiJodHRwOi8vZG9ja2V0dS5pdXRuYy51bml2LWxvcnJhaW5lLmZyOjYyMDE2IiwiaWF0IjoxNjQ4NjM2MDQ1LCJleHAiOjE2NTEyMjgwNDUsInVwciI6eyJ1c2VyX2lkIjoiMDVlZGY1NmQtOTQzNi00NWU5LThlMjMtNmM5YWFkMjRlZDA3IiwidXNlcl9mdWxsbmFtZSI6Ikx5biBNY1dhdGVycyIsInVzZXJfZW1haWwiOiJsbWN3YXRlcnNnQGNyZWF0aXZlY29tbW9ucy5vcmciLCJ1c2VyX3VzZXJuYW1lIjoibG1jd2F0ZXJzZyIsInVzZXJfaXNBZG1pbiI6MX19.BWQHMDLV4fYr6GPR6oMxcc8UcoexqUtpRTjOWry99d8"
     * },
     * "links": {
     *     "users": {
     *         "href": "/users/"
     *     },
     *     "self": {
     *         "href": "/users/05edf56d-9436-45e9-8e23-6c9aad24ed07/"
     *     }
     * }
     */
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


    /**
     * 
     * @api {POST} /authAdmin Admin Authenticate
     * @apiName AuthenticateAdmin
     * @apiGroup User
     * @apiVersion  1.0.0
     * 
     * @apiDescription Authentification d'un utilisateur ayant les droits administrateur
     * 
     * @apiHeader Basic_Auth Email de l'utilisateur administrateur
     *                     <br> Mot de passe de l'utilisateur
     * 
     * @apiHeaderExample {json} Header-Example:
     * {
     *   "Authorization": "Basic bWF4QG1heC5tYXg6bWF4(...)"
     * }
     *
     * @apiSuccess (Success (200)) {String} resfresh-token Refresh Token lié à l'utisateur
     * @apiSuccessExample Success-Response:
     *{
     * "refresh-token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vZG9ja2V0dS5pdXRuYy51b(...)"
     * }
     */
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
