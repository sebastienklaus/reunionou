<?php

namespace reu\events\app\controller;

use DateTime;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use reu\events\app\models\Events;
use reu\events\app\models\Messages;
use reu\events\app\models\Members;

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

    // Créer un event
    public function createMessage(Request $req, Response $resp, array $args): Response
    {

        // Récupération du body de la requête
        $message_req = $req->getParsedBody();
        
        
        // if ($req->getAttribute('has_errors')) {

        //     $errors = $req->getAttribute('errors');

        //     //? à mettre ailleurs ? Container ? Utils ? Maiddleware ? Errors ? Faire fonction + générique
        //     if (isset($errors['title'])) {
        //         $this->container->get('logger.error')->error("error input message title");
        //         return Writer::json_error($resp, 403, '"title" : invalid input, string expected');
        //     }
        //     if (isset($errors['description'])) {
        //         $this->container->get('logger.error')->error("error mail message description");
        //         return Writer::json_error($resp, 403, '"description" : invalid input, text format expected');
        //     }
        //     if (isset($errors['author'])) {
        //         $this->container->get('logger.error')->error("error input author UUID");
        //         return Writer::json_error($resp, 403, '"author" : invalid input. d-m-Y format expected : uuid');
        //     }
        //     // if (isset($errors['spot'])) {
        //     //     $this->container->get('logger.error')->error("error input livraison heure");
        //     //     return Writer::json_error($resp, 403, '"heure" : invalid input. H:i format expected');
        //     // }
        //     if (isset($errors['date'])) {
        //         ($this->container->get('logger.error'))->error("error input date message");
        //         return Writer::json_error($resp, 403, '"date" : invalid input. date exepected : d-m-y H:m:i');
        //     }
        // };

      

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
                    "updated_at" => $new_message->updated_at->format('Y-m-d H:i:s'),
                    "created_at" => $new_message->created_at->format('Y-m-d H:i:s'),
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

  
}
