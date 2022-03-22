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

            // Récupération de la route getmembersByEvent                            
            $pathFormembersByEvent = $this->container->router->pathFor(
                'getMembersByEvent',
                ['id' => $id_event]
            );


            // Création des liens hateos
            $hateoas = [
                "self" => ["href" => $pathFormembersByEvent],
            ];


            // Création du body de la réponse
            //? Renomer les keys ou laisser les noms issus de la DB ?
            $datas_resp = [
                "type" => "collection",
                "count" => $nbMember,
                "member" => $members_resp,
   
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
