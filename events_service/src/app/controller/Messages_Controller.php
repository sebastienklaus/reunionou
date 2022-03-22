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

    public function getMessage(Request $req, Response $resp, array $args): Response
    {
        $id_message = $args['id'];
        
        try {
            
            //* Modification TD4.2
            $message = Messages::select(['id', 'content', 'author', 'media', 'event_id', 'created_at', 'updated_at'])
            ->where('id', '=', $id_message)
            ->firstOrFail();

            //TODO Vérifier type de controle depuis réception base de donnée dans cours
            //TODO étape filtrage à garder ou améliorer ?
            $message_resp = [
                'id' => $message->id,
                'content' => $message->content,
                'author' => $message->author,
                'media' => $message->media,
                'event_id' => $message->event_id,
                'created_at' => $message->created_at,
                'updated_at' => $message->updated_at
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
                'author' => $message->author,
                'media' => $message->media,
                // 'event_id' => $message->event_id, //? to be or not to be ?
                'created_at' => $message->created_at,
                'updated_at' => $message->updated_at
            ];
        }

            // Récupération de la route getMessagesByEvent                            
            $pathForMessagesByEvent = $this->container->router->pathFor(
                'getMessagesByEvent',
                ['id' => $id_event]
            );


            // Création des liens hateos
            $hateoas = [
                "self" => ["href" => $pathForMessagesByEvent],
            ];


            // Création du body de la réponse
            //? Renomer les keys ou laisser les noms issus de la DB ?
            $datas_resp = [
                "type" => "ressource",
                "count" => $nbMessage,
                "message" => $messages_resp,
   
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

  
}
