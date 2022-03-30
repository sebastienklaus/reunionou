<?php

namespace reu\events\app\controller;

use DateInterval;
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
     * 
     * 
     * {
     * "user_id": "/users/b1858803-2305-47f4-be67-1efc10a91da7",
     * "event_id": "7be3efde-34b1-4cc8-a52f-1429bdee413b",
     * "pseudo": "HARMand"
     * }
     * 
     * @apiSuccess (Success (200)) {String} id ID du member
     * @apiSuccess (Success (200)) {String} user_id ID de l'utilisateur associé au member
     * @apiSuccess (Success (200)) {String} event_id ID de l'event associé au member
     * @apiSuccess (Success (200)) {String} pseudo Pseudo du member
     * @apiSuccess (Success (200)) {date} created_at Date de création du member
     * @apiSuccess (Success (200)) {date} updated_at Date de la dernière modification du member
     * 
     * @apiSuccessExample Success-Response:
     *  {
     * "type": "ressource",
     * "member": {
     *     "id": "a9840463-19a3-4d3c-9cc4-c12fdfed0c35",
     *     "user_id": "/users/b1858803-2305-47f4-be67-1efc10a91da7",
     *     "event_id": "7be3efde-34b1-4cc8-a52f-1429bdee413b",
     *     "pseudo": "HARMand",
     *     "updated_at": "2022-03-28 09:01:22",
     *     "created_at": "2022-03-28 09:01:22"
     *           }
     * }
     */
    public function createMember(Request $req, Response $resp, array $args): Response
    {

        // Récupération du body de la requête
        $member_req = $req->getParsedBody();

        if ($req->getAttribute('has_errors')) {

            $errors = $req->getAttribute('errors');

            if (isset($errors['user_id'])) {
                $this->container->get('logger.error')->error("error input member's user_id");
                return Writer::json_error($resp, 403, '"user_id" : invalid input, uuid expected');
            }
            if (isset($errors['event_id'])) {
                $this->container->get('logger.error')->error("error input member's event_id");
                return Writer::json_error($resp, 403, '"event_id" : invalid input, uuid expected');
            }
            if (isset($errors['pseudo'])) {
                $this->container->get('logger.error')->error("error input member's pseudo");
                return Writer::json_error($resp, 403, '"pseudo" : invalid input, String expected');
            }
            if (isset($errors['status'])) {
                $this->container->get('logger.error')->error("error input member's status");
                return Writer::json_error($resp, 403, '"status" : invalid input, -1,0 or 1 expected');
            }
        };

        try {

            // Création d'un message via le model
            $new_member = new Members();

            if (isset($member_req['user_id'])) {
                $check_member = Members::select()->where('user_id', $member_req['user_id'])->where('event_id', $member_req['event_id'])->first();
            } else {
                $check_member = Members::select()->where('user_id', 'd-e-f-a-u-l-t')->where('event_id', $member_req['event_id'])->first();
            }


            if (is_null($check_member)) {
                // Récupération de la fonction UUID generator depuis le container
                $new_uuid = $this->container->uuid;
                // génération id basé sur un aléa : UUID v4
                $new_member->id = $new_uuid(4);

                if (isset($member_req['user_id'])) {
                    $new_member->user_id = filter_var($member_req['user_id'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                }
                if (isset($member_req['event_id'])) {
                    $new_member->event_id = filter_var($member_req['event_id'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                }
                $new_member->pseudo = filter_var($member_req['pseudo'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                $new_member->status = filter_var($member_req['status'], FILTER_SANITIZE_NUMBER_FLOAT); //? Maybe there is a better way to controle -1,0 1

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
                        "status" => $new_member->status
                    ]
                ];

                $resp = Writer::json_output($resp, 201)
                    ->withAddedHeader('application-header', 'reuninou') // 201 : created
                    ->withHeader("Location", $pathForMember);

                $resp->getBody()->write(json_encode($datas_resp));

                return $resp;
            } else {
                return Writer::json_error($resp, 500, 'This member already exist');
            }
        } catch (ModelNotFoundException $e) {
            return Writer::json_error($resp, 404, 'Ressource not found : message ID = ' . $new_member->id);
        } catch (\Exception $th) {
            //todo : log Error
            return Writer::json_error($resp, 500, 'Server Error : Can\'t create member');
        }
        //
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
     * 
     * 
     * {
     * "user_id": "/users/b1858803-2305-47f4-be67-1efc10a91da7",
     * "event_id": "7be3efde-34b1-4cc8-a52f-1429bdee413b",
     * "pseudo": "HARMand"
     * }
     * 
     * @apiSuccess (Success (200)) {String} id ID du member
     * @apiSuccess (Success (200)) {String} user_id ID de l'utilisateur associé au member
     * @apiSuccess (Success (200)) {String} event_id ID de l'event associé au member
     * @apiSuccess (Success (200)) {String} pseudo Pseudo du member
     * @apiSuccess (Success (200)) {date} created_at Date de création du member
     * @apiSuccess (Success (200)) {date} updated_at Date de la dernière modification du member
     * 
     * @apiSuccessExample Success-Response:
     *  {
     * "type": "ressource",
     * "member": {
     *     "id": "a9840463-19a3-4d3c-9cc4-c12fdfed0c35",
     *     "user_id": "/users/b1858803-2305-47f4-be67-1efc10a91da7",
     *     "event_id": "7be3efde-34b1-4cc8-a52f-1429bdee413b",
     *     "pseudo": "HARMand",
     *     "updated_at": "2022-03-28 09:01:22",
     *     "created_at": "2022-03-28 09:01:22"
     *           }
     * }
     */
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
            if (isset($errors['status'])) {
                $this->container->get('logger.error')->error("error input member's status");
                return Writer::json_error($resp, 403, '"status" : invalid input, -1,0 or 1 expected');
            }
        };



        try {

            // Création d'un message via le model
            $member = Members::Select(['id', 'user_id', 'event_id', 'pseudo', 'updated_at', 'created_at'])->findOrFail($args['id']);;


            $member->user_id = filter_var($member_req['user_id'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $member->event_id = filter_var($member_req['event_id'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $member->pseudo = filter_var($member_req['pseudo'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $member->status = filter_var($member_req['status'], FILTER_SANITIZE_NUMBER_FLOAT); //? Maybe there is a better way to controle -1,0 1

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
                    "status" => $member->status
                ]
            ];

            $resp = Writer::json_output($resp, 200)
                ->withAddedHeader('application-header', 'reuninou') // 201 : created
                ->withHeader("Location", $pathForMember);

            $resp->getBody()->write(json_encode($datas_resp));

            return $resp;
        } catch (ModelNotFoundException $e) {
            return Writer::json_error($resp, 404, 'Ressource not found : member');
        } catch (\Exception $th) {
            return Writer::json_error($resp, 500, 'Server Error : Can\'t update member ');
        }
        //
    }

    /**
     * 
     * @api {GET} /members/{id} getMemberById
     * @apiName GetMemberById
     * @apiGroup Member
     * @apiVersion  1.0.0
     * 
     * @apiParam  {String} id ID du member
     * 
     * @apiSuccess (Success (200)) {String} id ID du member
     * @apiSuccess (Success (200)) {String} member_id ID de l'utilisateur associé au member
     * @apiSuccess (Success (200)) {String} event_id ID de l'event associé au member
     * @apiSuccess (Success (200)) {String} pseudo Pseudo du member
     * @apiSuccess (Success (200)) {date} created_at Date de création du member
     * @apiSuccess (Success (200)) {date} updated_at Date de la dernière modification du member
     * 
     * @apiSuccessExample Success-Response:
     *  {
     * "type": "ressource",
     * "member": {
     *     "id": "314fc1b2-a413-481e-b570-c9c2c52e9e07",
     *     "user_id": "/users/9c2eca18-28ba-4c36-90c7-f0cb1d5e122b",
     *     "event_id": "1590272b-2f38-4d59-9f49-fe0dde3e2ea1",
     *     "pseudo": "hmerwoody",
     *     "created_at": "2021-08-04 01:16:08",
     *     "updated_at": "2021-12-16 10:01:06"
     * }
     * ...
     */
    public function getMember(Request $req, Response $resp, array $args): Response
    {
        $id_member = $args['id'];

        try {

            //* Modification TD4.2
            $member = Members::select(['id', 'user_id', 'event_id', 'pseudo', 'created_at', 'updated_at', 'status'])
                ->where('id', '=', $id_member)
                ->firstOrFail();

            $member_resp = [
                'id' => $member->id,
                'user_id' => $member->user_id,
                'event_id' => $member->event_id, //? to be or not to be ?
                'pseudo' => $member->pseudo,
                'created_at' => $member->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $member->updated_at->format('Y-m-d H:i:s'),
                'status' => $member->status
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
     * @apiSuccess (Success (200)) {String} member_id ID de l'utilisateur associé au member
     * @apiSuccess (Success (200)) {String} event_id ID de l'event associé au member
     * @apiSuccess (Success (200)) {String} pseudo Pseudo du member
     * @apiSuccess (Success (200)) {date} created_at Date de création du member
     * @apiSuccess (Success (200)) {date} updated_at Date de la dernière modification du member
     * 
     * @apiSuccessExample Success-Response:
     *  {
     * "type": "ressource",
     * "member": {
     *     "id": "314fc1b2-a413-481e-b570-c9c2c52e9e07",
     *     "user_id": "/users/9c2eca18-28ba-4c36-90c7-f0cb1d5e122b",
     *     "event_id": "1590272b-2f38-4d59-9f49-fe0dde3e2ea1",
     *     "pseudo": "hmerwoody",
     *     "created_at": "2021-08-04 01:16:08",
     *     "updated_at": "2021-12-16 10:01:06"
     * }
     * ...
     */
    public function getMembersByEvent(Request $req, Response $resp, array $args): Response
    {
        $id_event = $args['id'];

        try {
            $event = Events::findOrFail($id_event);
            $members = $event->members()->select()->get();
            $nbMember = count($members);



            $members_resp = [];
            foreach ($members as $member) {

                $pathForMember = $this->container->router->pathFor(
                    'getMember',
                    ['id' => $member->id]
                );

                $members_resp[] = [
                    'id' => $member->id,
                    'user_id' => $member->user_id,
                    'event_id' => $member->event_id, //? to be or not to be ?
                    'pseudo' => $member->pseudo,
                    'created_at' => $member->created_at,
                    'updated_at' => $member->updated_at,
                    'status' => $member->status,
                    'links' => [
                        "self" => ["href" => $pathForMember]
                    ]
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

            $resp = Writer::json_output($resp, 200);

            $resp->getBody()->write(json_encode($datas_resp));

            return $resp;
        } catch (ModelNotFoundException $e) {

            $clientError = $this->container->clientError;
            return $clientError($req, $resp, 404, "member not found");
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
     * 
     * @apiSuccessExample Success-Response:
     *  {
     * "type": "member",
     * "member": {
     *     "id": "a9840463-19a3-4d3c-9cc4-c12fdfed0c35",
     *     "user_id": "/users/b1858803-2305-47f4-be67-1efc10a91da7",
     *     "event_id": "7be3efde-34b1-4cc8-a52f-1429bdee413b",
     *     "pseudo": "HARMand",
     *     "created_at": "2022-03-28T09:01:22.000000Z",
     *     "updated_at": "2022-03-28T09:01:22.000000Z"
     * },
     * "response": "member deleted"
     * }
     */
    public function deleteMemberById(Request $req, Response $resp, array $args): Response
    {
        $id_member = $args['id'] ?? null;
        try {
            $member = Members::findOrFail($id_member);
            if ($member->delete()) {
                $datas_resp = [
                    "type" => "member",
                    "member" => $member,
                    "response" => "member deleted",
                ];
            } else {
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
            foreach ($members as $member) {
                if ($member->delete()) {
                    $datas_resp = [
                        "type" => "member",
                        "member" => $member,
                        "response" => "member deleted"
                    ];
                } else {
                    $datas_resp = [
                        "type" => "member",
                        "member" => $member,
                        "response" => "member couldn't be deleted"
                    ];
                }
                $resp->getBody()->write(json_encode($datas_resp));
            }
            return writer::json_output($resp, 200);
        } catch (ModelNotFoundException $e) {
            $clientError = $this->container->clientError;
            return $clientError($req, $resp, 404, "Event not found");
        }
    }

    public function getMembersByUserId(Request $req, Response $resp, array $args): Response
    {
        $id_user = $args['id'];

        try {
            $user_members = Members::select()->where('user_id', $id_user)->get();
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
                    'event_id' => $member->event_id,
                    'pseudo' => $member->pseudo,
                    'created_at' => $member->created_at,
                    'updated_at' => $member->updated_at,
                    'status' => $member->status,
                    'links' => [
                        "self" => ["href" => $pathForMember],
                        "event" => ["href" => $pathForEvent]
                    ]
                ];
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


    public function getOneMember(Request $req, Response $resp, array $args): Response
    {
        try {
            $query = $req->getQueryParams();

            $event = Events::findOrFail($query['event'])->members();

            if (isset($query['pseudo']) && isset($query['user_id'])) {
                $clientError = $this->container->clientError;
                return $clientError($req, $resp, 404, "Can't search user_id & pseudo at the same time");
            } else {
                if (isset($query['pseudo'])) {
                    // $event = $event->with('members')->get();
                    $query['user_id'] = null;
                    $event = $event->where('pseudo', '=', $query['pseudo'])->first();
                }
                if (isset($query['user_id'])) {
                    $query['pseudo'] = null;
                    $event = $event->where('user_id', '=', $query['user_id'])->first();
                }

                $resp = Writer::json_output($resp, 200);

                $resp->getBody()->write(json_encode($event));

                return $resp;
            }
        } catch (ModelNotFoundException $e) {

            $clientError = $this->container->clientError;
            return $clientError($req, $resp, 404, "Member not found");
        }
    }



    public function getAllMembers(Request $req, Response $resp, array $args): Response
    {

        try {
            $allMembers = Members::select(['id', 'pseudo', 'updated_at', 'status'])
                ->where('updated_at', '<', date('Y-m-d H:i:s', strtotime('-6 months')))
                ->orderBy('updated_at')->get();
            $count = count($allMembers);

            $data = [
                'type' => 'collection',
                'count' => $count,
                'members' => $allMembers
            ];

            $resp = Writer::json_output($resp, 200);

            $resp->getBody()->write(json_encode($data));

            return $resp;
        } catch (ModelNotFoundException $e) {

            $clientError = $this->container->clientError;
            return $clientError($req, $resp, 404, "userid not found");
        }
    }
}
