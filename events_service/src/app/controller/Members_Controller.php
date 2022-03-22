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

class Members_Controller
{

    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }
    public function getMember(Request $req, Response $resp, array $args): Response
    {
        $id_member = $args['id'];
        
        try {
            
            //* Modification TD4.2
            $member = Members::select(['id', 'username', 'is_guest', 'event_id', 'created_at', 'updated_at'])
            ->where('id', '=', $id_member)
            ->firstOrFail();

            //TODO Vérifier type de controle depuis réception base de donnée dans cours
            //TODO étape filtrage à garder ou améliorer ?
            $member_resp = [
                'id' => $member->id,
                'username' => $member->username,
                'is_guest' => $member->is_guest,
                'event_id' => $member->event_id, //? to be or not to be ?
                'created_at' => $member->created_at,
                'updated_at' => $member->updated_at
            ];

            // Récupération de la route member                          
            $pathForMember = $this->container->router->pathFor(
                'getMember',
                ['id' => $id_member]
            );

            $pathForEvent = $this->container->router->pathFor(
                'getEvent',
                ['id' => $member->event_id] //! a supprimer si uuid members = uuid user
            );

            // Création des liens hateos
            $hateoas = [
                "self" => ["href" => $pathForMember],
                "event" => ["href" => $pathForEvent]
                //todo: ?? maybe "messages" => ["href" => $pathForMessagesByMember]
            ];


            // Création du body de la réponse
            //? Renomer les keys ou laisser les noms issus de la DB ?
            $datas_resp = [
                "type" => "ressource",
                "member" => $member_resp,
                "links" => $hateoas
            ];

            //? Ressources imbriquées ? à priori non.

            $resp = $resp->withStatus(200);
            $resp = $resp->withHeader("Content-Type", "application/json;charset=utf-8");
            
            $resp->getBody()->write(json_encode($datas_resp));

            return $resp;
        } catch (ModelNotFoundException $e) {

            $clientError = $this->container->clientError;
            return $clientError($req, $resp, 404, "Member not found");

        }
    }

   public function getMembersByEvent(Request $req, Response $resp, array $args): Response
    {
        $id_event = $args['id'];
        
        try {
            $event = Events::findOrFail($id_event);
            $members = $event->members()->select()->get();
            $nbMember = count($members);

        $members_resp = [];
        foreach ($members as $member) {
            $members_resp[] = [
                'id' => $member->id,
                'username' => $member->username,
                'is_guest' => $member->is_guest,
                'event_id' => $member->event_id, //? to be or not to be ?
                'created_at' => $member->created_at,
                'updated_at' => $member->updated_at
            ];
        }

            // Récupération de la route getMembersByEvent                            
            $pathForMembersByEvent = $this->container->router->pathFor(
                'getMembersByEvent',
                ['id' => $id_event]
            );

            $pathForEvent = $this->container->router->pathFor(
                'getEvent',
                ['id' => $id_event]
            );

            // Création des liens hateos
            $hateoas = [
                "self" => ["href" => $pathForMembersByEvent],
                "event" => ["href" => $pathForEvent]
            ];


            // Création du body de la réponse
            //? Renomer les keys ou laisser les noms issus de la DB ?
            $datas_resp = [
                "type" => "collection",
                "count" => $nbMember,
                "member" => $members_resp,
                "links" => $hateoas
   
            ];

            //? Ressources imbriquées ? à priori non.

            $resp = $resp->withStatus(200);
            $resp = $resp->withHeader("Content-Type", "application/json;charset=utf-8");
            
            $resp->getBody()->write(json_encode($datas_resp));

            return $resp;
        } catch (ModelNotFoundException $e) {

            $clientError = $this->container->clientError;
            return $clientError($req, $resp, 404, "member not found");

        }
    }
}
