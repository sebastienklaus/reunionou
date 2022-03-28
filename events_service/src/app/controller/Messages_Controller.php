<?php

namespace reu\events\app\controller;

use DateTime;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use reu\events\app\models\Events;
use reu\events\app\models\Messages;
use reu\events\app\models\Members;
use reu\events\app\models\MessageValidator;

use reu\events\app\utils\Writer;

use \Slim\Container;

use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

class Messages_Controller
{

    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * 
     * @api {POST} /messages createMessage
     * @apiName CreateMessage
     * @apiGroup Message
     * @apiVersion  1.0.0
     * 
     * @apiParam  {String} content Contenu du message 
     * @apiParam  {String} member_id ID du membre ayant créé le message
     * @apiParam  {String} event_id ID de l'event associé au message
     * @apiParam  {JSON} media Media contenu dans le message
     * 
     * @apiParamExample Success-Response:
     * {
     *   "content": "ceci est un message",
     *   "member_id": "b1858803-2305-47f4-be67-1efc10a91da7",
     *   "event_id": "e04cc94c-77a7-4671-8e52-34eb1d781d57",
     *   "media": "{\"name\":\"Charleville\",\"latitude\":64.9483319,\"longitude\":-145.3501717}"
     * }
     * 
     * @apiSuccess (Success (201)) {String} id ID du message
     * @apiSuccess (Success (201)) {String} content Contenu du message 
     * @apiSuccess (Success (201)) {String} member_id ID du membre ayant créé le message
     * @apiSuccess (Success (201)) {String} event_id ID de l'event associé au message
     * @apiSuccess (Success (201)) {JSON} media Media contenu dans le message
     * @apiSuccess (Success (201)) {date} created_at Date de création du message
     * @apiSuccess (Success (201)) {date} updated_at Date de la dernière modification du message
     * 
     * @apiSuccessExample Success-Response:
     * {
     * "type": "ressource",
     * "message": {
     *     "id": "2ade231a-11e5-49b6-bfbc-ac82cb406a7f",
     *     "content": "ceci est un message",
     *     "member_id": "b1858803-2305-47f4-be67-1efc10a91da7",
     *     "event_id": "e04cc94c-77a7-4671-8e52-34eb1d781d57",
     *     "media": "{&quot;name&quot;:&quot;Charleville&quot;,&quot;latitude&quot;:64.9483319,&quot;longitude&quot;:-145.3501717}",
     *     "updated_at": "28-03-2022 08:29:40",
     *     "created_at": "28-03-2022 08:29:40"
     *            }
     * }
     */
    public function createMessage(Request $req, Response $resp, array $args): Response
    {

        // Récupération du body de la requête
        $message_req = $req->getParsedBody();
        
        
        if ($req->getAttribute('has_errors')) {

            $errors = $req->getAttribute('errors');
        
            if (isset($errors['content'])) {
                $this->container->get('logger.error')->error("error input event content");
                return Writer::json_error($resp, 403, '"content" : invalid input, string expected');
            }
            if (isset($errors['member_id'])) {
                $this->container->get('logger.error')->error("error input event member_id");
                return Writer::json_error($resp, 403, '"member_id" : invalid input, valid member_id expected');
            }
            if (isset($errors['event_id'])) {
                $this->container->get('logger.error')->error("error input event event_id");
                return Writer::json_error($resp, 403, '"event_id" : invalid input, string expected');
            }
            if (isset($errors['media'])) {
                $this->container->get('logger.error')->error("error input event media");
                return Writer::json_error($resp, 403, '"media" : invalid input, valid JSON expected');
            }
            
        };

      

        try {
            
            // Création d'un message via le model
            $new_message = new Messages();
            
            // Récupération de la fonction UUID generator depuis le container
            $new_uuid = $this->container->uuid;
            // génération id basé sur un aléa : UUID v4
            $new_message->id = $new_uuid(4);

            $new_message->content = filter_var($message_req['content'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $new_message->member_id = filter_var($message_req['member_id'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $new_message->event_id = filter_var($message_req['event_id'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $new_message->media = filter_var($message_req['media'], FILTER_SANITIZE_FULL_SPECIAL_CHARS); //?to do verification json ?

            $new_message->save();

            // Récupération du path pour le location dans header
            $pathForMessage = $this->container->router->pathFor(
                'getMessage',
                ['id' => $new_message->id]
            );

            $datas_resp = [
                "type" => "ressource",
                "message" => [
                    "id" => $new_message->id,
                    "content" => $new_message->content,
                    "member_id" => $new_message->member_id,
                    "event_id" => $new_message->event_id,
                    "media" => $new_message->media,
                    "updated_at" => $new_message->updated_at->format('d-m-Y H:i:s'),
                    "created_at" => $new_message->created_at->format('d-m-Y H:i:s'),
                ]
            ];

            $resp = Writer::json_output($resp, 201)
                ->withAddedHeader('application-header', 'reuninou') // 201 : created
                ->withHeader("Location", $pathForMessage);

            $resp->getBody()->write(json_encode($datas_resp));

            return $resp;
        } catch (ModelNotFoundException $e) {
            //todo: logError
            return Writer::json_error($resp, 404, 'Ressource not found : message ID = ' . $new_message->id);
        } catch (\Exception $th) {
            //todo : log Error
            return Writer::json_error($resp, 500, 'Server Error : Can\'t create message ' . $th->getMessage());
        }
        //
    }

    /**
     * 
     * @api {GET} /messages/{id} getMessageById
     * @apiName GetMessageById
     * @apiGroup Message
     * @apiVersion  1.0.0
     * 
     * @apiParam  {String} id ID du message
     * 
     * @apiSuccess (Success (200)) {String} id ID du message
     * @apiSuccess (Success (200)) {String} content Contenu du message 
     * @apiSuccess (Success (200)) {String} member_id ID du membre ayant créé le message
     * @apiSuccess (Success (200)) {String} event_id ID de l'event associé au message
     * @apiSuccess (Success (200)) {JSON} media Media contenu dans le message
     * @apiSuccess (Success (200)) {date} created_at Date de création du message
     * @apiSuccess (Success (200)) {date} updated_at Date de la dernière modification du message
     * 
     * @apiSuccessExample Success-Response:
     * {
     * "type": "ressource",
     * "message": {
     *     "id": "2ade231a-11e5-49b6-bfbc-ac82cb406a7f",
     *     "content": "ceci est un message",
     *     "member_id": "b1858803-2305-47f4-be67-1efc10a91da7",
     *     "event_id": "e04cc94c-77a7-4671-8e52-34eb1d781d57",
     *     "media": "{&quot;name&quot;:&quot;Charleville&quot;,&quot;latitude&quot;:64.9483319,&quot;longitude&quot;:-145.3501717}",
     *     "updated_at": "28-03-2022 08:29:40",
     *     "created_at": "28-03-2022 08:29:40"
     *            }
     * }
     */
    public function getMessage(Request $req, Response $resp, array $args): Response
    {
        $id_message = $args['id'];
        
        try {
            
            //* Modification TD4.2
            $message = Messages::select(['id', 'content', 'member_id', 'event_id', 'media', 'created_at', 'updated_at'])
            ->where('id', '=', $id_message)
            ->firstOrFail();

            //TODO Vérifier type de controle depuis réception base de donnée dans cours
            //TODO étape filtrage à garder ou améliorer ?
            $message_resp = [
                'id' => $message->id,
                'content' => $message->content,
                'member_id' => $message->member_id,
                'event_id' => $message->event_id,
                'media' => $message->media,
                'updated_at' => $message->updated_at->format('Y-m-d H:i:s'),
                'created_at' => $message->created_at->format('Y-m-d H:i:s')
            ];

            // Récupération de la route events                            
            $pathForMessage = $this->container->router->pathFor(
                'getMessage',
                ['id' => $id_message]
            );

            //todo: links hatoas pour autheur vers ficher members
            //todo: pareil pour lien event

            // // Récupération de la route              
            // //? route Event Members + Messages pour hateoas ?                  
            $pathForEventOfMessage = $this->container->router->pathFor(
                'getEvent',
                ['id' => $message->event_id]
            );

            // $pathForMemberOfMessage = $this->container->router->pathFor(
            //     'getMembersByEvent',
            //     ['id' => $author]
            // );

            // Création des liens hateos
            $hateoas = [
                "self" => ["href" => $pathForMessage],
                "event" => ["href" => $pathForEventOfMessage],
                // "messages" => ["href" => $pathForMessagesByMessage]
            ];


            // Création du body de la réponse
            //? Renomer les keys ou laisser les noms issus de la DB ?
            $datas_resp = [
                "type" => "ressource",
                "message" => $message_resp,
                "links" => $hateoas
            ];

            //? Ressources imbriquées ? à priori non.

            $resp = $resp->withStatus(200);
            $resp = $resp->withHeader("Content-Type", "application/json;charset=utf-8");
            
            $resp->getBody()->write(json_encode($datas_resp));

            return $resp;
        } catch (ModelNotFoundException $e) {

            $clientError = $this->container->clientError;
            return $clientError($req, $resp, 404, "Message not found");

        }
    }

    /**
     * 
     * @api {GET} /events/{id}/messages getMessagesByEventId
     * @apiName GetMessagesByEventId   
     * @apiGroup Message
     * @apiVersion  1.0.0
     * 
     * @apiParam  {String} id ID de l'event
     * 
     * @apiSuccess (Success (200)) {String} id ID du message
     * @apiSuccess (Success (200)) {String} content Contenu du message 
     * @apiSuccess (Success (200)) {String} member_id ID du membre ayant créé le message
     * @apiSuccess (Success (200)) {String} event_id ID de l'event associé au message
     * @apiSuccess (Success (200)) {JSON} media Media contenu dans le message
     * @apiSuccess (Success (200)) {date} created_at Date de création du message
     * @apiSuccess (Success (200)) {date} updated_at Date de la dernière modification du message
     * 
     * @apiSuccessExample Success-Response:
     *  {
     * "type": "collection",
     * "count": 2,
     * "message": [
     *     {
     *         "id": "559407d8-73c1-4251-8fab-f9413a48fec6",
     *         "content": "In est risus, auctor sed, tristique in, tempus sit amet, sem. Fusce consequat. Nulla nisl.",
     *         "member_id": "1190e0fc-524f-4c32-a087-4bed56091978",
     *         "event_id": "0447ff47-e257-4bfc-b1a6-913a2c6cbd79",
     *         "media": "{\"img\":\"http://dummyimage.com/122x100.png/cc0000/ffffff\",\"link\":\"http://dummyimage.com/249x100.png/5fa2dd/ffffff\"}",
    *          "updated_at": "2021-09-16 10:10:39",
     *         "created_at": "2021-04-03 12:07:25"
     *     }
     *     ...
     */
    public function getMessagesByEvent(Request $req, Response $resp, array $args): Response
    {
        $id_event = $args['id'];
        
        try {
            $event = Events::findOrFail($id_event);
            $messages = $event->messages()->select()->get();
            $nbMessage = count($messages);

        $messages_resp = [];
        foreach ($messages as $message) {
            $messages_resp[] = [
             'id' => $message->id,
                'content' => $message->content,
                'member_id' => $message->member_id,
                'event_id' => $message->event_id,
                'media' => $message->media,
                'updated_at' => $message->updated_at->format('Y-m-d H:i:s'),
                'created_at' => $message->created_at->format('Y-m-d H:i:s')
            ];
        }

            // Récupération de la route getMessagesByEvent                            
            $pathForMessagesByEvent = $this->container->router->pathFor(
                'getMessagesByEvent',
                ['id' => $id_event]
            );

            $pathForEvent = $this->container->router->pathFor(
                'getEvent',
                ['id' => $id_event]
            );

            // Création des liens hateos
            $hateoas = [
                "self" => ["href" => $pathForMessagesByEvent],
                "event" => ["href" => $pathForEvent]
            ];


            // Création du body de la réponse
            //? Renomer les keys ou laisser les noms issus de la DB ?
            $datas_resp = [
                "type" => "collection",
                "count" => $nbMessage,
                "message" => $messages_resp,
                "links" => $hateoas
   
            ];

            //? Ressources imbriquées ? à priori non.

            $resp = $resp->withStatus(200);
            $resp = $resp->withHeader("Content-Type", "application/json;charset=utf-8");
            
            $resp->getBody()->write(json_encode($datas_resp));

            return $resp;
        } catch (ModelNotFoundException $e) {

            $clientError = $this->container->clientError;
            return $clientError($req, $resp, 404, "Event not found");

        }
    }

    /**
     * 
     * @api {DELETE} /messages/{id} deleteMessage
     * @apiName DeleteMessageById
     * @apiGroup Message
     * @apiVersion  1.0.0
     * 
     * @apiParam  {String} id ID du message
     * 
     * @apiSuccess (Success (200)) {String} id ID du message
     * @apiSuccess (Success (200)) {String} content Contenu du message 
     * @apiSuccess (Success (200)) {String} member_id ID du membre ayant créé le message
     * @apiSuccess (Success (200)) {String} event_id ID de l'event associé au message
     * @apiSuccess (Success (200)) {JSON} media Media contenu dans le message
     * @apiSuccess (Success (200)) {date} created_at Date de création du message
     * @apiSuccess (Success (200)) {date} updated_at Date de la dernière modification du message
     * 
     * @apiSuccessExample Success-Response:
     *  {
     * "type": "message",
     * "message": {
     *     "id": "559407d8-73c1-4251-8fab-f9413a48fec6",
     *     "content": "In est risus, auctor sed, tristique in, tempus sit amet, sem. Fusce consequat. Nulla nisl.",
     *     "member_id": "1190e0fc-524f-4c32-a087-4bed56091978",
     *     "event_id": "0447ff47-e257-4bfc-b1a6-913a2c6cbd79",
     *     "media": "{\"img\":\"http://dummyimage.com/122x100.png/cc0000/ffffff\",\"link\":\"http://dummyimage.com/249x100.png/5fa2dd/ffffff\"}",
     *     "created_at": "2021-04-03T12:07:25.000000Z",
     *     "updated_at": "2021-09-16T10:10:39.000000Z"
     * },
     * "response": "message deleted"
     * }
     */
    public function deleteMessageById(Request $req, Response $resp, array $args): Response
    {
        $id_message = $args['id'] ?? null;
        try {
            $message = Messages::findOrFail($id_message);
            if ($message->delete())
            {
                $datas_resp = [
                    "type" => "message",
                    "message" => $message,
                    "response" => "message deleted"
                ];
            } else
            {
                $datas_resp = [
                    "type" => "message",
                    "message" => $message,
                    "response" => "message couldn't be deleted"
                ];
            }
            $resp->getBody()->write(json_encode($datas_resp));
            return writer::json_output($resp, 200);
        } catch (ModelNotFoundException $e) {
            return Writer::json_error($resp, 404, "message not found");
        }
    }

    public function deleteMessagesByEvent(Request $req, Response $resp, array $args): Response
    {
        $id_event = $args['id'];
        
        try {
            $event = Events::findOrFail($id_event);
            $messages = $event->messages()->select()->get();
            $nbMessage = count($messages);

        $messages_resp = [];
        // TODO : Faire en une seule requête
        foreach ($messages as $message) {
                if ($message->delete())
                {
                    $datas_resp = [
                        "type" => "message",
                        "message" => $message,
                        "response" => "message deleted"
                    ];
                } else
                {
                    $datas_resp = [
                        "type" => "message",
                        "message" => $message,
                        "response" => "message couldn't be deleted"
                    ];
                }
                $resp->getBody()->write(json_encode($datas_resp));
            }
            return writer::json_output($resp, 200); 
        }catch (ModelNotFoundException $e) {
            $clientError = $this->container->clientError;
            return $clientError($req, $resp, 404, "Event not found");
        }
    }
}

