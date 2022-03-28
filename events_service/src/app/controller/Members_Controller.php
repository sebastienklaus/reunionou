<?php

namespace reu\events\app\controller;

use DateTime;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use reu\events\app\models\Events;
use reu\events\app\models\Messages;
use reu\events\app\models\Members;
use reu\events\app\models\MemberValidator;

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

    // Créer un event
    public function createMember(Request $req, Response $resp, array $args): Response
    {

        // Récupération du body de la requête
        $member_req = $req->getParsedBody();
        
            if ($req->getAttribute('has_errors')) {

                $errors = $req->getAttribute('errors');
            
                if (isset($errors['user_id'])) {
                    $this->container->get('logger.error')->error("error input event user_id");
                    return Writer::json_error($resp, 403, '"user_id" : invalid input, valid user_id expected');
                }
                if (isset($errors['event_id'])) {
                    $this->container->get('logger.error')->error("error input event event_id");
                    return Writer::json_error($resp, 403, '"event_id" : invalid input, string expected');
                }
                if (isset($errors['pseudo'])) {
                    $this->container->get('logger.error')->error("error input event pseudo");
                    return Writer::json_error($resp, 403, '"pseudo" : invalid input, valid pseudo expected');
                }
                
            };

        try {
            
            // Création d'un message via le model
            $new_member = new Members();
            
            // Récupération de la fonction UUID generator depuis le container
            $new_uuid = $this->container->uuid;
            // génération id basé sur un aléa : UUID v4
            $new_member->id = $new_uuid(4);

            $new_member->user_id = filter_var($member_req['user_id'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $new_member->event_id = filter_var($member_req['event_id'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $new_member->pseudo = filter_var($member_req['pseudo'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            $new_member->save();

            // Récupération du path pour le location dans header
            $pathForMember = $this->container->router->pathFor(
                'getMember',
                ['id' => $new_member->id]
            );

            $datas_resp = [
                "type" => "ressource",
                "member" => [
                    "id" => $new_member->id,
                    "user_id" => $new_member->user_id,
                    "event_id" => $new_member->event_id,
                    "pseudo" => $new_member->pseudo,
                    "updated_at" => $new_member->updated_at->format('Y-m-d H:i:s'),
                    "created_at" => $new_member->created_at->format('Y-m-d H:i:s'),
                ]
            ];

            $resp = Writer::json_output($resp, 201)
                ->withAddedHeader('application-header', 'reuninou') // 201 : created
                ->withHeader("Location", $pathForMember);

            $resp->getBody()->write(json_encode($datas_resp));

            return $resp;
        } catch (ModelNotFoundException $e) {
            //todo: logError
            return Writer::json_error($resp, 404, 'Ressource not found : message ID = ' . $new_member->id);
        } catch (\Exception $th) {
            //todo : log Error
            return Writer::json_error($resp, 500, 'Server Error : Can\'t create member ' . $th->getMessage());
        }
        //
    }

        // Créer un event
        public function updateMember(Request $req, Response $resp, array $args): Response
        {
    
            // Récupération du body de la requête
            $member_req = $req->getParsedBody();
            
            
            if ($req->getAttribute('has_errors')) {

                $errors = $req->getAttribute('errors');
            
                if (isset($errors['user_id'])) {
                    $this->container->get('logger.error')->error("error input event user_id");
                    return Writer::json_error($resp, 403, '"user_id" : invalid input, valid user_id expected');
                }
                if (isset($errors['event_id'])) {
                    $this->container->get('logger.error')->error("error input event event_id");
                    return Writer::json_error($resp, 403, '"event_id" : invalid input, string expected');
                }
                if (isset($errors['pseudo'])) {
                    $this->container->get('logger.error')->error("error input event pseudo");
                    return Writer::json_error($resp, 403, '"pseudo" : invalid input, valid pseudo expected');
                }
                
            };
    
          
    
            try {
                
                // Création d'un message via le model
                $member = Members::Select(['id', 'user_id', 'event_id', 'pseudo', 'updated_at', 'created_at'])->findOrFail($args['id']);;
                
    
                $member->user_id = filter_var($member_req['user_id'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                $member->event_id = filter_var($member_req['event_id'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                $member->pseudo = filter_var($member_req['pseudo'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    
                $member->save();
    
                // Récupération du path pour le location dans header
                $pathForMember = $this->container->router->pathFor(
                    'getMember',
                    ['id' => $member->id]
                );
    
                $datas_resp = [
                    "type" => "ressource",
                    "member" => [
                        "id" => $member->id,
                        "user_id" => $member->user_id,
                        "event_id" => $member->event_id,
                        "pseudo" => $member->pseudo,
                        "updated_at" => $member->updated_at->format('Y-m-d H:i:s'),
                        "created_at" => $member->created_at->format('Y-m-d H:i:s'),
                    ]
                ];
    
                $resp = Writer::json_output($resp, 200)
                    ->withAddedHeader('application-header', 'reuninou') // 201 : created
                    ->withHeader("Location", $pathForMember);
    
                $resp->getBody()->write(json_encode($datas_resp));
    
                return $resp;
            } catch (ModelNotFoundException $e) {
                //todo: logError
                return Writer::json_error($resp, 404, 'Ressource not found : member ID = ' . $member->id);
            } catch (\Exception $th) {
                //todo : log Error
                return Writer::json_error($resp, 500, 'Server Error : Can\'t update member ' . $th->getMessage());
            }
            //
        }

    public function getMember(Request $req, Response $resp, array $args): Response
    {
        $id_member = $args['id'];
        
        try {
            
            //* Modification TD4.2
            $member = Members::select(['id', 'user_id', 'event_id', 'pseudo', 'created_at', 'updated_at'])
            ->where('id', '=', $id_member)
            ->firstOrFail();

            //TODO Vérifier type de controle depuis réception base de donnée dans cours
            //TODO étape filtrage à garder ou améliorer ?
            $member_resp = [
                'id' => $member->id,
                'user_id' => $member->user_id,
                'event_id' => $member->event_id, //? to be or not to be ?
                'pseudo' => $member->pseudo,
                'created_at' => $member->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $member->updated_at->format('Y-m-d H:i:s')
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

            $resp = Writer::json_output($resp, 200);
            
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
                'user_id' => $member->user_id,
                'event_id' => $member->event_id, //? to be or not to be ?
                'pseudo' => $member->pseudo,
                'created_at' => $member->created_at,
                'updated_at' => $member->updated_at,
                'status' => $member->status
            ]; // TODO rajouter lien self pour chaque member
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

            $resp = Writer::json_output($resp, 200);
            
            $resp->getBody()->write(json_encode($datas_resp));

            return $resp;
        } catch (ModelNotFoundException $e) {

            $clientError = $this->container->clientError;
            return $clientError($req, $resp, 404, "member not found");

        }
    }

    public function deleteMemberById(Request $req, Response $resp, array $args): Response
    {
        $id_member = $args['id'] ?? null;
        try {
            $member = Members::findOrFail($id_member);
            if ($member->delete())
            {
                $datas_resp = [
                    "type" => "member",
                    "member" => $member,
                    "response" => "member deleted",
                ];
            } else
            {
                $datas_resp = [
                    "type" => "member",
                    "member" => $member,
                    "response" => "member couldn't be deleted"
                ];
            }
            $resp->getBody()->write(json_encode($datas_resp));
            return writer::json_output($resp, 200);
        } catch (ModelNotFoundException $e) {
            return Writer::json_error($resp, 404, "member not found");
        }
    }

    public function deleteMembersByEvent(Request $req, Response $resp, array $args): Response
    {
        $id_event = $args['id'];
        
        try {
            $event = Events::findOrFail($id_event);
            $members = $event->members()->select()->get();
            $nbMember = count($members);

        $messages_resp = [];
        // TODO : Faire en une seule requête
        foreach ($members as $member) {
                if ($member->delete())
                {
                    $datas_resp = [
                        "type" => "member",
                        "member" => $member,
                        "response" => "member deleted"
                    ];
                } else
                {
                    $datas_resp = [
                        "type" => "member",
                        "member" => $member,
                        "response" => "member couldn't be deleted"
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

    public function getMembersByUserId(Request $req, Response $resp, array $args): Response
    {
        $id_user = $args['id'];
        
        try {
            $user_members = Members::select()->where('user_id','like','%' . $id_user)->get();
            $nbUser_Member = count($user_members);

        $user_members_resp = [];
        foreach ($user_members as $member) {

            $pathForMember = $this->container->router->pathFor(
                'getMember',
                ['id' => $member->id]
            );

            $pathForEvent = $this->container->router->pathFor(
                'getEvent',
                ['id' => $member->event_id]
            );

            $user_members_resp[] = [
                'id' => $member->id,
                'user_id' => $member->user_id,
                'event_id' => $member->event_id, //? to be or not to be ?
                'pseudo' => $member->pseudo,
                'created_at' => $member->created_at,
                'updated_at' => $member->updated_at,
                'links' => [
                    "self" => ["href" => $pathForMember],
                    "event" => ["href" => $pathForEvent]
                ]
            ]; // TODO rajouter lien self pour chaque member
        }

            // Création du body de la réponse
            //? Renomer les keys ou laisser les noms issus de la DB ?
            $datas_resp = [
                "type" => "collection",
                "count" => $nbUser_Member,
                "member" => $user_members_resp,
   
            ];

            $resp = Writer::json_output($resp, 200);
            
            $resp->getBody()->write(json_encode($datas_resp));

            return $resp;
        } catch (ModelNotFoundException $e) {

            $clientError = $this->container->clientError;
            return $clientError($req, $resp, 404, "userid not found");

        }
    }


    public function getOneMember(Request $req, Response $resp, array $args): Response {

        $query= $req->getQueryParams();

        // $event = Events::select();
        $event = Events::findOrFail($query['event'])->members();
        
        if (isset($query['pseudo'])) {
            // $event = $event->with('members')->get();
            $query['user_id'] = null;
            $event = $event->where('pseudo', 'like', '%'.$query['pseudo'])->first();
        }
        if (isset($query['user_id'])) {
            $query['pseudo'] = null;
            $event = $event->where('user_id', 'like', '%/users/'.$query['user_id'])->first();
        }

        // $event = $event->get();
        $resp = Writer::json_output($resp, 200);
            
        $resp->getBody()->write(json_encode($event));

        return $resp;
    }
    
}
