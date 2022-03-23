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

    // Créer un event
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

            //? à mettre ailleurs ? Container ? Utils ? Maiddleware ? Errors ? Faire fonction + générique
            if (isset($errors['title'])) {
                $this->container->get('logger.error')->error("error input event title");
                return Writer::json_error($resp, 403, '"title" : invalid input, string expected');
            }
            if (isset($errors['description'])) {
                $this->container->get('logger.error')->error("error mail event description");
                return Writer::json_error($resp, 403, '"description" : invalid input, text format expected');
            }
            if (isset($errors['author'])) {
                $this->container->get('logger.error')->error("error input author UUID");
                return Writer::json_error($resp, 403, '"author" : invalid input. d-m-Y format expected : uuid');
            }
            // if (isset($errors['spot'])) {
            //     $this->container->get('logger.error')->error("error input livraison heure");
            //     return Writer::json_error($resp, 403, '"heure" : invalid input. H:i format expected');
            // }
            if (isset($errors['date'])) {
                ($this->container->get('logger.error'))->error("error input date event");
                return Writer::json_error($resp, 403, '"date" : invalid input. date exepected : d-m-y H:m:i');
            }
        };


        try {
            
            // Création d'un event via le model
            $new_event = new Events();
            
            // Récupération de la fonction UUID generator depuis le container
            $new_uuid = $this->container->uuid;
            // génération id basé sur un aléa : UUID v4
            $new_event->id = $new_uuid(4);
            
            // //Récupération de la fonction token depuis le container
            // $new_token = $this->container->token;
            // // Génération token
            // $new_event->token = $new_token(32);

            $new_event->title = filter_var($event_req['title'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $new_event->description = filter_var($event_req['description'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $new_event->author = filter_var($event_req['author'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            //! FILTE POUR SPOT : SPOT JSON DOIT ETRE OK
            $new_event->spot = $event_req['spot'];
            // Création de la date  de livraison
            $date_event = new DateTime($event_req['date']);
            $new_event->date = $date_event->format('Y-m-d H:i:s');


            $new_event->save();

            // Récupération du path pour le location dans header
            $pathForEvent = $this->container->router->pathFor(
                'getEvent',
                ['id' => $new_event->id]
            );

            $datas_resp = [
                "type" => "ressource",
                "event" => [
                    "title" => $new_event->title,
                    "description" => $new_event->description,
                    "date" => $new_event->date,
                    "author" => $new_event->author,
                    // "token" => $new_event->token,
                    "date" => $new_event->date
                ]
            ];

            $resp = Writer::json_output($resp, 201)
                ->withAddedHeader('application-header', 'reuninou') // 201 : created
                ->withHeader("Location", $pathForEvent);

            $resp->getBody()->write(json_encode($datas_resp));

            return $resp;
        } catch (ModelNotFoundException $e) {
            //todo: logError
            return Writer::json_error($resp, 404, 'Ressource not found : event ID = ' . $new_evente->id);
        } catch (\Exception $th) {
            //todo : log Error
            return Writer::json_error($resp, 500, 'Server Error : Can\'t create event');
        }
        //
    }

    
    public function getEvent(Request $req, Response $resp, array $args): Response
    {
        $id_event = $args['id'];
        
        try {
            
            //* Modification TD4.2
            $event = Events::select(['id', 'title', 'description', 'author', 'spot', 'date', 'created_at', 'updated_at'])
            ->where('id', '=', $id_event)
            ->firstOrFail();

            //TODO Vérifier type de controle depuis réception base de donnée dans cours
            //TODO étape filtrage à garder ou améliorer ?
            $event_resp = [
                'id' => $event->id,
                'title' => $event->title,
                'description' => $event->description,
                'author' => $event->author,
                'spot' => $event->spot,
                'date' => $event->date,
                'created_at' => $event->created_at,
                'updated_at' => $event->updated_at
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

            $resp = $resp->withStatus(200);
            $resp = $resp->withHeader("Content-Type", "application/json;charset=utf-8");
            
            $resp->getBody()->write(json_encode($datas_resp));

            return $resp;
        } catch (ModelNotFoundException $e) {

            $clientError = $this->container->clientError;
            return $clientError($req, $resp, 404, "Event not found");


            // return Writer::json_error($resp, 404, "Alors j'ai bien regardé, j'ai pas trouvé ta commande");
        }
    }

    // // Toutes les commandes
    public function getAllEvent(Request $req, Response $resp): Response
    {
        //todo: try catch

        // Récupérer les commandes depuis le model
        $events = Events::select(['id', 'title', 'description', 'author', 'spot', 'date', 'created_at', 'updated_at'])
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
                'author' => $event->author,
                'spot' => $event->spot,
                'date' => $event->date,
                'created_at' => $event->created_at,
                'updated_at' => $event->updated_at //?rajouter un link avec pathfor ?
            ];
        }

        // Construction des donnés à retourner dans le body
        $datas_resp = [
            "type" => "collection",
            "count" => $nbEvents,
            "events" => $events_resp
        ];

        $resp = $resp->withStatus(200);
        $resp = $resp->withHeader("Content-Type", "application/json;charset=utf-8");

        $resp->getBody()->write(json_encode($datas_resp));

        return $resp;
    }

    // // Remplacer une commande. PUT, pas PATCH !!
    // public function putCommande(Request $req, Response $resp, array $args): Response
    // {

    //     $commande_data = $req->getParsedBody();

    //     $clientError = $this->container->clientError;

    //     if (!isset($commande_data['nom_client'])) {
    //         return $clientError($req, $resp, 400, "Missing 'nom_client");
    //         // return Writer::json_error($resp, 400, "missing 'nom_client'");
    //     };

    //     if (!isset($commande_data['mail_client'])) {
    //         return Writer::json_error($resp, 400, "missing 'mail_client'");
    //     };

    //     if (!isset($commande_data['livraison']['date'])) {
    //         return Writer::json_error($resp, 400, "missing 'livraison(date)'");
    //     };

    //     if (!isset($commande_data['livraison']['heure'])) {
    //         return Writer::json_error($resp, 400, "missing 'livraison(heure)'");
    //     };

    //     try {
    //         // Récupérer la commande
    //         $commande = Commande::Select(['id', 'nom', 'mail', 'livraison'])->findOrFail($args['id']);

    //         $commande->nom = filter_var($commande_data['nom_client'], FILTER_SANITIZE_STRING);
    //         $commande->mail = filter_var($commande_data['mail_client'], FILTER_SANITIZE_EMAIL);
    //         $commande->livraison = DateTime::createFromFormat(
    //             'Y-m-d H:i',
    //             $commande_data['livraison']['date'] . ' ' .
    //                 $commande_data['livraison']['heure']
    //         );

    //         $commande->save();

    //         return Writer::json_output($resp, 204);
    //     } catch (ModelNotFoundException $e) {
    //         return Writer::json_error($resp, 404, "commande inconnue : {$args}");
    //     } catch (\Exception $e) {
    //         return Writer::json_error($resp, 500, $e->getMessage());
    //     }






    //     return $resp;
    // }
}
