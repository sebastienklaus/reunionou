<?php

namespace reu\events\app\controller;

use DateTime;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use reu\events\app\models\Events;
use reu\events\app\models\Messages;
use reu\events\app\models\Members;
use reu\events\app\models\EventValidator;

use reu\events\app\utils\Writer;

use \Slim\Container;

use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

class Events_Controller
{

    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * 
     * @api {POST} /events createEvent
     * @apiName CreateEvent
     * @apiGroup Event
     * @apiVersion  1.0.0
     * 
     * 
     * @apiParam  {String} title Titre de l'event
     * @apiParam  {String} description Description de l'event
     * @apiParam  {String} user_id ID de l'utilisateur ayant créé l'event
     * @apiParam  {json} location Lieu de l'event
     * @apiParam  {date} date Date de l'event
     * @apiParam  {date} heure Heure de l'event
     *
     * @apiParamExample Request-Example:
     *{
     *"title" : "Event_Name",
     *"description" : "Event_Description",
     *"user_id" : "/users/User_Id",
     *"location" : {
     *    "name":"Location_Name",
     *    "latitude":00.00,
     *    "longitude":00.00
     *    },
     *"date" : "2000-01-01",
     *"heure" : "12:00"
     *}
     *  
     * @apiSuccess (Success (201)) {String} title Titre de l'event
     * @apiSuccess (Success (201)) {String} description Description de l'event
     * @apiSuccess (Success (201)) {String} user_id ID de l'utilisateur ayant créé l'event
     * @apiSuccess (Success (201)) {json} location Lieu de l'event
     * @apiSuccess (Success (201)) {date} date Date de l'event
     * @apiSuccess (Success (201)) {date} heure Heure de l'event
     * @apiSuccess (Success (201)) {date} created_at Date de création de l'event
     * @apiSuccess (Success (201)) {date} updated_at Date de la dernière modification de l'event
     * 
     * @apiSuccessExample Success-Response:
     *{
     *"type": "ressource",
     *"event": {
     *    "id": "Event_Id",
     *    "title": "Event_Name",
     *    "description": "Event_Description",
     *    "user_id": "/users/User_Id",
     *    "location": "{\"name\":\"Location_Name\",\"latitude\":00.00,\"longitude\":00.00}",
     *    "date": "2000-01-01",
     *    "heure": "12:00:00",
     *    "created_at": "Creation_Date",
     *    "updated_at": "Creation_Date"
     *    }
     *}
     */
    public function createEvent(Request $req, Response $resp, array $args): Response
    {

        //TODO: - Création d'une nvlle commande => génération d'un token unique, cryptographique, retourné dans la rep
        //TODO: et utilisé pour valider les prochaines requête de cette même commande
        //TODO: Remplace FILTER_SANITIZE_STRING par htmlentities, ou htmlspecialchars (check param) ou strip_tags.
        //? check_Token : middleware, mais createToken-> middleware ??

        // Récupération du body de la requête
        $event_req = $req->getParsedBody();
        
        
        if ($req->getAttribute('has_errors')) {

            $errors = $req->getAttribute('errors');

            if (isset($errors['title'])) {
                $this->container->get('logger.error')->error("error input event title");
                return Writer::json_error($resp, 403, '"title" : invalid input, string expected');
            }
            if (isset($errors['description'])) {
                $this->container->get('logger.error')->error("error mail event description");
                return Writer::json_error($resp, 403, '"description" : invalid input, text format expected');
            }
            if (isset($errors['user_id'])) {
                $this->container->get('logger.error')->error("error input user_id UUID");
                return Writer::json_error($resp, 403, '"user_id" : invalid input. invalid input, valid format expected : uuid');
            }
            if (isset($errors['location'])) {
                $this->container->get('logger.error')->error("error input livraison location");
                return Writer::json_error($resp, 403, '"location" : invalid input, valid format expected');
            }
            if (isset($errors['date'])) {
                ($this->container->get('logger.error'))->error("error input date date");
                return Writer::json_error($resp, 403, '"date" : invalid input, format exepected : Y-m-d');
            }
            if (isset($errors['heure'])) {
                ($this->container->get('logger.error'))->error("error input date heure");
                return Writer::json_error($resp, 403, '"heure" : invalid input. format exepected : H:m:i');
            }
        };

        try {
            
            // Création d'un event via le model
            $new_event = new Events();
            
            // Récupération de la fonction UUID generator depuis le container
            $new_uuid = $this->container->uuid;
            // génération id basé sur un aléa : UUID v4
            $new_event->id = $new_uuid(4);

            $new_event->title = filter_var($event_req['title'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $new_event->description = filter_var($event_req['description'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $new_event->user_id = filter_var($event_req['user_id'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            //! FILTE POUR location : location JSON DOIT ETRE OK
            $new_event->location = $event_req['location'];
            // Création de la date  de livraison
            $date_event = new DateTime($event_req['date']);
            $new_event->date = $date_event->format('Y-m-d');
            $heure_event = new DateTime($event_req['heure']);
            $new_event->heure = $heure_event->format('H:i:s');


            $new_event->save();

            // Récupération du path pour le location dans header
            $pathForEvent = $this->container->router->pathFor(
                'getEvent',
                ['id' => $new_event->id]
            );

            $datas_resp = [
                "type" => "ressource",
                "event" => [
                    "id" => $new_event->id,
                    "title" => $new_event->title,
                    "description" => $new_event->description,
                    "user_id" => $new_event->user_id,
                    "location" => $new_event->location,
                    "date" => $new_event->date,
                    "heure" => $new_event->heure,
                    "created_at" => $new_event->created_at->format('Y-m-d H:i:s'),
                    "updated_at" => $new_event->updated_at->format('Y-m-d H:i:s')
                ]
            ];

            $resp = Writer::json_output($resp, 201)
                ->withAddedHeader('application-header', 'reuninou') // 201 : created
                ->withHeader("Location", $pathForEvent);

            $resp->getBody()->write(json_encode($datas_resp));

            return $resp;
        } catch (ModelNotFoundException $e) {
            //todo: logError
            return Writer::json_error($resp, 404, 'Ressource not found : event ID = ' . $new_event->id);
        } catch (\Exception $th) {
            //todo : log Error
            return Writer::json_error($resp, 500, 'Server Error : Can\'t create event');
        }
        //
    }


    /**
     * 
     * @api {PUT} /events updateEvent
     * @apiName UpdateEvent
     * @apiGroup Event
     * @apiVersion  1.0.0
     * 
     * 
     * @apiParam  {String} title
     * @apiParam  {String} description
     * @apiParam  {String} user_id
     * @apiParam  {json} location
     * @apiParam  {date} date
     * @apiParam  {date} heure
     * 
     * @apiParamExample Request-Example:
     *{
     *"title" : "Event_Name",
     *"description" : "Event_Description",
     *"user_id" : "/users/User_Id",
     *"location" : {
     *    "name":"Location_Name",
     *    "latitude":00.00,
     *    "longitude":00.00
     *    },
     *"date" : "2000-01-01",
     *"heure" : "12:00"
     *}
     * 
     * @apiSuccess (Success (201)) {String} title Titre de l'event
     * @apiSuccess (Success (201)) {String} description Description de l'event
     * @apiSuccess (Success (201)) {String} user_id ID de l'utilisateur ayant créé l'event
     * @apiSuccess (Success (201)) {json} location Lieu de l'event
     * @apiSuccess (Success (201)) {date} date Date de l'event
     * @apiSuccess (Success (201)) {date} heure Heure de l'event
     * @apiSuccess (Success (201)) {date} created_at Date de création de l'event
     * @apiSuccess (Success (201)) {date} updated_at Date de la dernière modification de l'event
     * 
     * @apiSuccessExample Success-Response:
     *{
     *"type": "ressource",
     *"event": {
     *    "id": "Event_Id",
     *    "title": "Event_Name",
     *    "description": "Event_Description",
     *    "user_id": "/users/User_Id",
     *    "location": "{\"name\":\"Location_Name\",\"latitude\":00.00,\"longitude\":00.00}",
     *    "date": "2000-01-01",
     *    "heure": "12:00:00",
     *    "created_at": "Creation_Date",
     *    "updated_at": "Modification_Date"
     *    }
     *}
     */
    public function updateEvent(Request $req, Response $resp, array $args): Response
    {

        // Récupération du body de la requête
        $received_event = $req->getParsedBody();
        
        if ($req->getAttribute('has_errors')) {

            $errors = $req->getAttribute('errors');

            if (isset($errors['title'])) {
                $this->container->get('logger.error')->error("error input event title");
                return Writer::json_error($resp, 403, '"title" : invalid input, string expected');
            }
            if (isset($errors['description'])) {
                $this->container->get('logger.error')->error("error mail event description");
                return Writer::json_error($resp, 403, '"description" : invalid input, text format expected');
            }
            if (isset($errors['user_id'])) {
                $this->container->get('logger.error')->error("error input user_id UUID");
                return Writer::json_error($resp, 403, '"user_id" : invalid input. invalid input, valid format expected : uuid');
            }
            if (isset($errors['location'])) {
                $this->container->get('logger.error')->error("error input livraison location");
                return Writer::json_error($resp, 403, '"location" : invalid input, valid format expected');
            }
            if (isset($errors['date'])) {
                ($this->container->get('logger.error'))->error("error input date date");
                return Writer::json_error($resp, 403, '"date" : invalid input, format exepected : Y-m-d');
            }
            if (isset($errors['heure'])) {
                ($this->container->get('logger.error'))->error("error input date heure");
                return Writer::json_error($resp, 403, '"heure" : invalid input. format exepected : H:m:i');
            }
        };

        try {

            $event = Events::Select(['id', 'title', 'description', 'user_id', 'location', 'date', 'heure'])->findOrFail($args['id']);

            $event->title = filter_var($received_event['title'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $event->description = filter_var($received_event['description'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $event->user_id = filter_var($received_event['user_id'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            //! FILTE POUR location : location JSON DOIT ETRE OK
            $event->location = $received_event['location'];
            // Création de la date  de livraison
            $date_event = new DateTime($received_event['date']);
            $event->date = $date_event->format('Y-m-d');
            $heure_event = new DateTime($received_event['heure']);
            $event->heure = $heure_event->format('H:i:s');


            $event->save();

            // Récupération du path pour le location dans header
            $pathForEvent = $this->container->router->pathFor(
                'getEvent',
                ['id' => $event->id]
            );

            $datas_resp = [
                "type" => "ressource",
                "event" => [
                    "id" => $event->id,
                    "title" => $event->title,
                    "description" => $event->description,
                    "user_id" => $event->user_id,
                    "location" => $event->location,
                    "date" => $event->date,
                    "heure" => $event->heure,
                    "created_at" => $event->created_at,
                    "updated_at" => $event->updated_at
                ]
            ];

            $resp = Writer::json_output($resp, 200)
                ->withAddedHeader('application-header', 'reuninou')
                ->withHeader("Location", $pathForEvent);

            $resp->getBody()->write(json_encode($datas_resp));

            return $resp;
        } catch (ModelNotFoundException $e) {
            //todo: logError
            return Writer::json_error($resp, 404, 'Ressource not found : event ID = ' . $event->id);
        } catch (\Exception $th) {
            //todo : log Error
            return Writer::json_error($resp, 500, 'Server Error : Can\'t update event');
        }
        //
    }

    /**
     * 
     * @api {GET} /events/{id} getEventById
     * @apiName GetEventById
     * @apiGroup Event
     * @apiVersion  1.0.0
     * 
     * @apiParam  {id} id id de l'évènement
     * 
     * @apiSuccess (Success (200)) {String} title Titre de l'event
     * @apiSuccess (Success (200)) {String} description Description de l'event
     * @apiSuccess (Success (200)) {String} user_id ID de l'utilisateur ayant créé l'event
     * @apiSuccess (Success (200)) {json} location Lieu de l'event
     * @apiSuccess (Success (200)) {date} date Date de l'event
     * @apiSuccess (Success (200)) {date} heure Heure de l'event
     * @apiSuccess (Success (200)) {date} created_at Date de création de l'event
     * @apiSuccess (Success (200)) {date} updated_at Date de la dernière modification de l'event
     * 
     * @apiSuccessExample Success-Response:
     *{
     *"type": "ressource",
     *"event": {
     *    "id": "Event_Id",
     *    "title": "Event_Name",
     *    "description": "Event_Description",
     *    "user_id": "/users/User_Id",
     *    "location": "{\"name\":\"Location_Name\",\"latitude\":00.00,\"longitude\":00.00}",
     *    "date": "2000-01-01",
     *    "heure": "12:00:00",
     *    "created_at": "Creation_Date",
     *    "updated_at": "Modification_Date"
     *    }
     *}
     */
    public function getEvent(Request $req, Response $resp, array $args): Response
    {
        $id_event = $args['id'];
        
        try {
            
            $event = Events::select(['id', 'title', 'description', 'user_id', 'location', 'date', 'heure', 'created_at', 'updated_at'])
            ->where('id', '=', $id_event)
            ->firstOrFail();

            //TODO Vérifier type de controle depuis réception base de donnée dans cours
            //TODO étape filtrage à garder ou améliorer ?

            $event_resp = [
                'id' => $event->id,
                'title' => $event->title,
                'description' => $event->description,
                'user_id' => $event->user_id,
                'location' => json_decode($event->location),
                'date' => $event->date,
                'heure' => $event->heure,
                'created_at' => $event->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $event->updated_at->format('Y-m-d H:i:s')
            ];

            // Récupération de la route events                            
            $pathForEvent = $this->container->router->pathFor(
                'getEvent',
                ['id' => $id_event]
            );

            // // Récupération de la route              
            // //? route Event Members + Messages pour hateoas ?                  
            $pathForMessagesByEvent = $this->container->router->pathFor(
                'getMessagesByEvent',
                ['id' => $id_event]
            );

            $pathForMembersByEvent = $this->container->router->pathFor(
                'getMembersByEvent',
                ['id' => $id_event]
            );

            // Création des liens hateos
            $hateoas = [
                "self" => ["href" => $pathForEvent],
                "members" => ["href" => $pathForMembersByEvent],
                "messages" => ["href" => $pathForMessagesByEvent]
            ];


            // Création du body de la réponse
            //? Renomer les keys ou laisser les noms issus de la DB ?
            $datas_resp = [
                "type" => "ressource",
                "event" => $event_resp,
                "links" => $hateoas
            ];

            //? Ressources imbriquées ? à priori non.

            $resp = Writer::json_output($resp, 200);
            
            $resp->getBody()->write(json_encode($datas_resp));

            return $resp;
        } catch (ModelNotFoundException $e) {

            $clientError = $this->container->clientError;
            return $clientError($req, $resp, 404, "Event not found");


            // return Writer::json_error($resp, 404, "Alors j'ai bien regardé, j'ai pas trouvé ta commande");
        }
    }

    /**
     * 
     * @api {GET} /members/{pseudo}/events getEventByMemberPseudo
     * @apiName GetEventByMemberPseudo
     * @apiGroup Event
     * @apiVersion  1.0.0
     * 
     * @apiParam  {pseudo} pseudo Pseudo du membre
     * 
     * @apiSuccess (Success (200)) {String} title Titre de l'event
     * @apiSuccess (Success (200)) {String} description Description de l'event
     * @apiSuccess (Success (200)) {String} user_id ID de l'utilisateur ayant créé l'event
     * @apiSuccess (Success (200)) {json} location Lieu de l'event
     * @apiSuccess (Success (200)) {date} date Date de l'event
     * @apiSuccess (Success (200)) {date} heure Heure de l'event
     * @apiSuccess (Success (200)) {date} created_at Date de création de l'event
     * @apiSuccess (Success (200)) {date} updated_at Date de la dernière modification de l'event
     * 
     * @apiSuccessExample Success-Response:
     *{
     *"type": "ressource",
     *"event": {
     *    "id": "Event_Id",
     *    "title": "Event_Name",
     *    "description": "Event_Description",
     *    "user_id": "/users/User_Id",
     *    "location": "{\"name\":\"Location_Name\",\"latitude\":00.00,\"longitude\":00.00}",
     *    "date": "2000-01-01",
     *    "heure": "12:00:00",
     *    "created_at": "Creation_Date",
     *    "updated_at": "Modification_Date"
     *    }
     *}
     */
    public function getEventByMemberPseudo(Request $req, Response $resp, array $args): Response
    {
        $pseudo_member = $args['pseudo'];
        
        try {
            $event_id_list = Members::select(['event_id'])
                                    ->where('pseudo', '=', $pseudo_member)
                                    ->get();

            $events = Events::select(['id', 'title', 'description', 'user_id', 'location', 'date', 'heure', 'created_at', 'updated_at'])
                        ->whereIn('id', $event_id_list)
                        ->get();

        //TODO Vérifier type de controle depuis réception base de donnée dans cours
        //TODO étape filtrage à garder ou améliorer ?
            $nbEvents = count($events);
            $events_resp = [];
            foreach ($events as $event) {
                $events_resp[] = [
                'id' => $event->id,
                'title' => $event->title,
                'description' => $event->description,
                'user_id' => $event->user_id,
                'location' => json_decode($event->location),
                'date' => $event->date,
                'heure' => $event->heure,
                'created_at' => $event->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $event->updated_at->format('Y-m-d H:i:s'), //?rajouter un link avec pathfor ?
                'href' => $this->container->router->pathFor('getEvent',['id' => $event->id])
                ];
            }

        // Construction des donnés à retourner dans le body
        $datas_resp = [
            "type" => "collection",
            "count" => $nbEvents,
            "events" => $events_resp
        ];

        $resp = Writer::json_output($resp, 200);

        $resp->getBody()->write(json_encode($datas_resp));

        return $resp;
    } catch (ModelNotFoundException $e) {

            $clientError = $this->container->clientError;
            return $clientError($req, $resp, 404, "Event not found");


            // return Writer::json_error($resp, 404, "Alors j'ai bien regardé, j'ai pas trouvé ta commande");
        }
    }

    /**
     * 
     * @api {GET} /users/{id}/events getEventByUserId
     * @apiName GetEventByUserId
     * @apiGroup Event
     * @apiVersion  1.0.0
     * 
     * @apiParam  {id} id ID de l'utilisateur
     * 
     * @apiSuccess (Success (200)) {String} title Titre de l'event
     * @apiSuccess (Success (200)) {String} description Description de l'event
     * @apiSuccess (Success (200)) {String} user_id ID de l'utilisateur ayant créé l'event
     * @apiSuccess (Success (200)) {json} location Lieu de l'event
     * @apiSuccess (Success (200)) {date} date Date de l'event
     * @apiSuccess (Success (200)) {date} heure Heure de l'event
     * @apiSuccess (Success (200)) {date} created_at Date de création de l'event
     * @apiSuccess (Success (200)) {date} updated_at Date de la dernière modification de l'event
     * 
     * @apiSuccessExample Success-Response:
     *{
     *"type": "ressource",
     *"event": {
     *    "id": "Event_Id",
     *    "title": "Event_Name",
     *    "description": "Event_Description",
     *    "user_id": "/users/User_Id",
     *    "location": "{\"name\":\"Location_Name\",\"latitude\":00.00,\"longitude\":00.00}",
     *    "date": "2000-01-01",
     *    "heure": "12:00:00",
     *    "created_at": "Creation_Date",
     *    "updated_at": "Modification_Date"
     *    }
     *}
     */
    public function getEventByUserId(Request $req, Response $resp, array $args): Response
    {
        $user_id = $args['id'];
        
        try {
            $event_id_list = Events::select(['id'])
                                    ->where('user_id', 'like', '%'.$user_id)
                                    ->get();

            $events = Events::select(['id', 'title', 'description', 'user_id', 'location', 'date', 'heure', 'created_at', 'updated_at'])
                        ->whereIn('id', $event_id_list)
                        ->get();

            $nbEvents = count($events);
            $events_resp = [];
            foreach ($events as $event) {
                $events_resp[] = [
                'id' => $event->id,
                'title' => $event->title,
                'description' => $event->description,
                'user_id' => $event->user_id,
                'location' => json_decode($event->location),
                'date' => $event->date,
                'heure' => $event->heure,
                'created_at' => $event->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $event->updated_at->format('Y-m-d H:i:s'),
                'href' => $this->container->router->pathFor('getEvent',['id' => $event->id])
                ];
            }

        // Construction des donnés à retourner dans le body
        $datas_resp = [
            "type" => "collection",
            "count" => $nbEvents,
            "events" => $events_resp
        ];

        $resp = Writer::json_output($resp, 200);

        $resp->getBody()->write(json_encode($datas_resp));

        return $resp;
    } catch (ModelNotFoundException $e) {

            $clientError = $this->container->clientError;
            return $clientError($req, $resp, 404, "Event not found");


            // return Writer::json_error($resp, 404, "Alors j'ai bien regardé, j'ai pas trouvé ta commande");
        }
    }

    /**
     * 
     * @api {GET} /events getAllEvent
     * @apiName GetAllEvent
     * @apiGroup Event
     * @apiVersion  1.0.0
     * 
     * @apiSuccess (Success (200)) {String} title Titre de l'event
     * @apiSuccess (Success (200)) {String} description Description de l'event
     * @apiSuccess (Success (200)) {String} user_id ID de l'utilisateur ayant créé l'event
     * @apiSuccess (Success (200)) {json} location Lieu de l'event
     * @apiSuccess (Success (200)) {date} date Date de l'event
     * @apiSuccess (Success (200)) {date} heure Heure de l'event
     * @apiSuccess (Success (200)) {date} created_at Date de création de l'event
     * @apiSuccess (Success (200)) {date} updated_at Date de la dernière modification de l'event
     * 
     * @apiSuccessExample Success-Response:
     *{
     *"type": "ressource",
     *"event": {
     *    "id": "Event_Id",
     *    "title": "Event_Name",
     *    "description": "Event_Description",
     *    "user_id": "/users/User_Id",
     *    "location": "{\"name\":\"Location_Name\",\"latitude\":00.00,\"longitude\":00.00}",
     *    "date": "2000-01-01",
     *    "heure": "12:00:00",
     *    "created_at": "Creation_Date",
     *    "updated_at": "Modification_Date"
     *    }
     *}
     */
    public function getAllEvent(Request $req, Response $resp): Response
    {
        //todo: try catch

        // Récupérer les commandes depuis le model
        $events = Events::select(['id', 'title', 'description', 'user_id', 'location', 'date', 'heure', 'created_at', 'updated_at'])
                          ->get();

        //TODO Vérifier type de controle depuis réception base de donnée dans cours
        //TODO étape filtrage à garder ou améliorer ?
        $nbEvents = count($events);
        $events_resp = [];
        foreach ($events as $event) {
            $events_resp[] = [
                'id' => $event->id,
                'title' => $event->title,
                'description' => $event->description,
                'user_id' => $event->user_id,
                'location' => json_decode($event->location),
                'date' => $event->date,
                'heure' => $event->heure,
                'created_at' => $event->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $event->updated_at->format('Y-m-d H:i:s'), //?rajouter un link avec pathfor ?
                'href' => $this->container->router->pathFor('getEvent',['id' => $event->id])
            ];
        }

        // Construction des donnés à retourner dans le body
        $datas_resp = [
            "type" => "collection",
            "count" => $nbEvents,
            "events" => $events_resp
        ];

        $resp = Writer::json_output($resp, 200);

        $resp->getBody()->write(json_encode($datas_resp));

        return $resp;
    }

    public function deleteEventById(Request $req, Response $resp, array $args): Response
    {
        $id_event = $args['id'] ?? null;
        try {
            $event = Events::findOrFail($id_event);
            $members = $event->members()->select()->get();
            $messages = $event->messages()->select()->get();
            if ($event->delete() && $members->delete() && $messages->delete()) 
            {
                $datas_resp = [
                    "type" => "event",
                    "event" => $event,
                    "response" => "event deleted",
                ];
            } else 
            {
                $datas_resp = [
                    "type" => "event",
                    "event" => $event,
                    "response" => "event couldn't be deleted"
                ];
            }
            // ! Fonctionne mais erreur HTML
            $resp->getBody()->write(json_encode($datas_resp));
            return writer::json_output($resp, 200);
        } catch (ModelNotFoundException $e) {
            return Writer::json_error($resp, 404, "event not found");
        }
    }
}
