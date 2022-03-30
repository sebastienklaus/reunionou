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

    /**
     * 
     * @api {GET} /members/{id} getMember(renomer tout)
     * @apiName GetMemberById
     * @apiGroup Member
     * @apiVersion  1.0.0
     * 
     * @apiParam  {String} id ID du member
     * 
     * @apiSuccess (Success (200)) {String} id ID du member
     * @apiSuccess (Success (200)) {String} user_id ID de l'utilisateur associé au member
     * @apiSuccess (Success (200)) {String} event_id ID de l'event associé au member
     * @apiSuccess (Success (200)) {String} pseudo Pseudo du member
     * @apiSuccess (Success (200)) {date} created_at Date de création du member
     * @apiSuccess (Success (200)) {date} updated_at Date de la dernière modification du member
     * @apiSuccess (Success (200)) {Number} status Status du member (-1: en attente, 0: annulé, 1: confirmé )
     * 
     * @apiSuccessExample Success-Response:
     * {
     *    "type": "ressource",
     *    "member": {
     *        "id": "9d7761ea-17a7-483b-b0c5-ff740cfc74c1",
     *        "user_id": "",
     *        "event_id": "abb18196-937c-483a-a7c5-f77337c14f8b",
     *        "pseudo": "mmitchell1j",
     *        "created_at": "2021-09-02 04:55:10",
     *        "updated_at": "2022-03-29 14:16:25",
     *        "status": 1
     *    },
     *    "links": {
     *        "self": {
     *            "href": "/members/9d7761ea-17a7-483b-b0c5-ff740cfc74c1/"
     *        },
     *        "event": {
     *            "href": "/events/abb18196-937c-483a-a7c5-f77337c14f8b/"
     *        }
     *    }
     *}
     */
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

    /**
     * 
     * @api {POST} /members createMember
     * @apiName CreateMember
     * @apiGroup Member
     * @apiVersion  1.0.0
     * 
     * @apiParam {String} user_id ID de l'utilisateur associé au member
     * @apiParam {String} event_id ID de l'event associé au member
     * @apiParam {String} pseudo Pseudo du member
     * @apiParam {Number} status du member (-1: en attente de réponse, 0: annulé, 1: invitation accepté )
     * 
     * @apiParamExample Request-Example:
     * {
     * "user_id": "/users/b1858803-2305-47f4-be67-1efc10a91da7",
     * "event_id": "7be3efde-34b1-4cc8-a52f-1429bdee413b",
     * "pseudo": "User_5",
     * "status": -1
     * }
     * 
     * @apiSuccess (Success (201)) {String} id ID du member
     * @apiSuccess (Success (201)) {String} user_id ID de l'utilisateur associé au member
     * @apiSuccess (Success (201)) {String} event_id ID de l'event associé au member
     * @apiSuccess (Success (201)) {String} pseudo Pseudo du member
     * @apiSuccess (Success (201)) {date} created_at Date de création du member
     * @apiSuccess (Success (201)) {date} updated_at Date de la dernière modification du member
     * @apiSuccess (Success (201)) {Number} status Status du member
     * 
     * @apiSuccessExample Success-Response:
     *  {
     * "type": "ressource",
     * "member": {
     *     "id": "a9840463-19a3-4d3c-9cc4-c12fdfed0c35",
     *     "user_id": "b1858803-2305-47f4-be67-1efc10a91da7",
     *     "event_id": "7be3efde-34b1-4cc8-a52f-1429bdee413b",
     *     "pseudo": "User_5",
     *     "updated_at": "2022-03-28 09:01:22",
     *     "created_at": "2022-03-28 09:01:22",
     *     "status": "-1"
     *           }
     * }
     */
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

    /**
     * 
     * @api {PUT} /members/{id} updateMember
     * @apiName UpdateMember
     * @apiGroup Member
     * @apiVersion  1.0.0
     * 
     * @apiParam {String} user_id ID de l'utilisateur associé au member
     * @apiParam {String} event_id ID de l'event associé au member
     * @apiParam {String} pseudo Pseudo du member
     * @apiParam {String} status du member (-1: en attente de réponse, 0: annulé, 1: invitation accepté )
     * 
     * @apiParamExample Request-Example:
     * {
     * "user_id": "/users/b1858803-2305-47f4-be67-1efc10a91da7",
     * "event_id": "7be3efde-34b1-4cc8-a52f-1429bdee413b",
     * "pseudo": "user_7"
     * }
     * 
     * @apiSuccess (Success (200)) {String} id ID du member
     * @apiSuccess (Success (200)) {String} user_id ID de l'utilisateur associé au member
     * @apiSuccess (Success (200)) {String} event_id ID de l'event associé au member
     * @apiSuccess (Success (200)) {String} pseudo Pseudo du member
     * @apiSuccess (Success (200)) {date} created_at Date de création du member
     * @apiSuccess (Success (200)) {date} updated_at Date de la dernière modification du member
     * @apiSuccess (Success (200)) {Number} status Status du member
     * 
     * @apiSuccessExample Success-Response:
     *  {
     * "type": "ressource",
     * "member": {
     *     "id": "a9840463-19a3-4d3c-9cc4-c12fdfed0c35",
     *     "user_id": "/users/b1858803-2305-47f4-be67-1efc10a91da7",
     *     "event_id": "7be3efde-34b1-4cc8-a52f-1429bdee413b",
     *     "pseudo": "user_7",
     *     "updated_at": "2022-03-28 09:01:22",
     *     "created_at": "2022-03-28 09:01:22",
     *     "status": -1
     *           },
     *  "links": {
     *      "self": {
     *          "href": "/members/a9840463-19a3-4d3c-9cc4-c12fdfed0c35/"
     *      },
     *      "event": {
     *          "href": "/events/7be3efde-34b1-4cc8-a52f-1429bdee413b/"
     *      }
     * }
     */
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
            return Writer::json_error_data($resp, 500, "$responseBodyAsString");
        } catch (\Exception $e) {
            return Writer::json_error($resp, 500, "Server Error: can't update member");
        }
    }

    /**
     * 
     * @api {GET} /events/{id}/members getMembersByEventId
     * @apiName GetMembersByEventId
     * @apiGroup Member
     * @apiVersion  1.0.0
     * 
     * @apiParam  {String} id ID de l'event
     * 
     * @apiSuccess (Success (200)) {String} id ID du member
     * @apiSuccess (Success (200)) {String} user_id ID de l'utilisateur associé au member
     * @apiSuccess (Success (200)) {String} event_id ID de l'event associé au member
     * @apiSuccess (Success (200)) {String} pseudo Pseudo du member
     * @apiSuccess (Success (200)) {date} created_at Date de création du member
     * @apiSuccess (Success (200)) {date} updated_at Date de la dernière modification du member
     * @apiSuccess (Success (200)) {Number} status Status du member (-1: en attente, 0: annulé, 1: confirmé )
     * 
     * @apiSuccessExample Success-Response:
     *  {
     * "type": "collection",
     * "count": 3,
     * "member": [
     *     {
     *         "id": "314fc1b2-a413-481e-b570-c9c2c52e9e07",
     *         "user_id": "poulette",
     *         "event_id": "7be3efde-34b1-4cc8-a52f-1429bdee413b",
     *         "pseudo": "HARMand",
     *         "created_at": "2021-08-04T01:16:08.000000Z",
     *         "updated_at": "2022-03-29T14:28:32.000000Z",
     *         "status": 0,
     *         "links": {
     *             "self": {
     *                 "href": "/members/314fc1b2-a413-481e-b570-c9c2c52e9e07/"
     *             }
     *         }
     *     },
     *     {
     *         "id": "9a715068-bf27-467b-b291-0148b40fd70a",
     *         "user_id": "898f20c4-710d-4508-9129-af7a975572ae",
     * ...
     */
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

    /**
     * 
     * @api {DELETE} /members/{id} deleteMember
     * @apiName DeleteMemberById
     * @apiGroup Member
     * @apiVersion  1.0.0
     * 
     * @apiParam  {String} id ID du member
     * 
     * @apiSuccess (Success (200)) {String} id ID du member
     * @apiSuccess (Success (200)) {String} user_id ID de l'utilisateur ayant créé le member
     * @apiSuccess (Success (200)) {String} event_id ID de l'event associé au member
     * @apiSuccess (Success (200)) {JSON} media Media contenu dans le member
     * @apiSuccess (Success (200)) {date} created_at Date de création du member
     * @apiSuccess (Success (200)) {date} updated_at Date de la dernière modification du member
     * @apiSuccess (Success (200)) {Number} status Status du member (-1: en attente, 0: annulé, 1: confirmé )
     * 
     * @apiSuccessExample Success-Response:
     *  {
     * "type": "member",
     * "member": {
     *     "id": "a9840463-19a3-4d3c-9cc4-c12fdfed0c35",
     *     "user_id": "/users/b1858803-2305-47f4-be67-1efc10a91da7",
     *     "event_id": "7be3efde-34b1-4cc8-a52f-1429bdee413b",
     *     "pseudo": "User_24",
     *     "created_at": "2022-03-28T09:01:22.000000Z",
     *     "updated_at": "2022-03-28T09:01:22.000000Z",
     *     "status": -1
     * },
     * "response": "member deleted"
     * }
     */
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

    /**
     * 
     * @api {GET} /members/{id} getMemberByUserID
     * @apiName GetMemberByUserID
     * @apiGroup Member
     * @apiVersion  1.0.0
     * 
     * @apiDescription Récupérer tous les membres pour un utilisateur enregistré
     * 
     * @apiParam  {String} id ID du User
     * 
     * @apiSuccess (Success (200)) {String} id ID du member
     * @apiSuccess (Success (200)) {String} user_id ID de l'utilisateur associé au member
     * @apiSuccess (Success (200)) {String} event_id ID de l'event associé au member
     * @apiSuccess (Success (200)) {String} pseudo Pseudo du member
     * @apiSuccess (Success (200)) {date} created_at Date de création du member
     * @apiSuccess (Success (200)) {date} updated_at Date de la dernière modification du member
     * @apiSuccess (Success (200)) {Number} status Status du member (-1: en attente, 0: annulé, 1: confirmé )
     * 
     * @apiSuccessExample Success-Response:
     * {
     * "type": "collection",
     *     "count": 1,
     *     "member": [
     *         {
     *             "id": "9a715068-bf27-467b-b291-0148b40fd70a",
     *             "user_id": "898f20c4-710d-4508-9129-af7a975572ae",
     *             "event_id": "7be3efde-34b1-4cc8-a52f-1429bdee413b",
     *             "pseudo": "ijanatam",
     *             "created_at": "2022-01-24T01:17:29.000000Z",
     *             "updated_at": "2021-11-15T17:16:47.000000Z",
     *             "status": -1,
     *             "links": {
     *                 "self": {
     *                     "href": "/members/9a715068-bf27-467b-b291-0148b40fd70a/"
     *                 },
     *                 "event": {
     *                     "href": "/events/7be3efde-34b1-4cc8-a52f-1429bdee413b/"
     *                 }
     *             }
     *         }
     *     ]
     *}
     */
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


    /**
     * 
     * @api {GET} /members/{id}/?event_id&pseudo&user_id getOneMember
     * @apiName GetOneMember
     * @apiGroup Member
     * @apiVersion  1.0.0
     * 
     * @apiDescription Récupérer un membre selon un évenement et selon son urder_id OU son pseudo
     * 
     * @apiParam  {String} id ID du User
     * @apiParam  {String} event_id ID de l'événement
     * @apiParam  {String} pseudo String Pseudo du membre
     * @apiParam  {String} user_id String user associé au membre
     * 
     * @apiSuccess (Success (200)) {String} id ID du member
     * @apiSuccess (Success (200)) {String} user_id ID de l'utilisateur associé au member
     * @apiSuccess (Success (200)) {String} event_id ID de l'event associé au member
     * @apiSuccess (Success (200)) {String} pseudo Pseudo du member
     * @apiSuccess (Success (200)) {date} created_at Date de création du member
     * @apiSuccess (Success (200)) {date} updated_at Date de la dernière modification du member
     * @apiSuccess (Success (200)) {Number} status Status du member (-1: en attente, 0: annulé, 1: confirmé )
     * 
     * @apiSuccessExample Success-Response:
     * {
     * "type": "collection",
     *     "count": 1,
     *     "member": [
     *         {
     *             "id": "9a715068-bf27-467b-b291-0148b40fd70a",
     *             "user_id": "898f20c4-710d-4508-9129-af7a975572ae",
     *             "event_id": "7be3efde-34b1-4cc8-a52f-1429bdee413b",
     *             "pseudo": "ijanatam",
     *             "created_at": "2022-01-24T01:17:29.000000Z",
     *             "updated_at": "2021-11-15T17:16:47.000000Z",
     *             "status": -1
     *         }
     *     ]
     *}
     */
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
