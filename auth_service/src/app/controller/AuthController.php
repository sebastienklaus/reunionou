<?php

namespace reunionou\auth\app\controller;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

//JWT classes
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException ;
use Firebase\JWT\BeforeValidException;

use Illuminate\Database\Eloquent\ModelNotFoundException;

// Custom classes
use reunionou\auth\app\model\User as User;
use reunionou\auth\app\error\Writer as Writer;

class AuthController {

    private $container; // le conteneur de dépendences de l'application
    

    public function __construct(\Slim\Container $container)
    {
        $this->container = $container;
    }

    public function authenticate(Request $req, Response $resp, $args): Response {

        if (!$req->hasHeader('Authorization')) {

            $resp = $resp->withHeader('WWW-authenticate', 'Basic realm="commande_api api" ');
            return Writer::jsonError($req, $resp, 401, 'No Authorization header present');
        };

        $authstring = base64_decode(explode(" ", $req->getHeader('Authorization')[0])[1]);
        list($email, $pass) = explode(':', $authstring);

        try {
            $user = User::select()
                ->where('email', '=', $email)
                ->firstOrFail();

            if (!password_verify($pass, $user->password))
                throw new \Exception("password check failed");

            unset($user->password);

        } catch (ModelNotFoundException $e) {
            $resp = $resp->withHeader('WWW-authenticate', 'Basic realm="reunionou auth" ');
            return Writer::jsonError($req, $resp, 401, 'Cet utilisateur n\'existe pas');
        } catch (\Exception $e) {
            $resp = $resp->withHeader('WWW-authenticate', 'Basic realm="reunionou auth" ');
            return Writer::jsonError($req, $resp, 401, 'Erreur authentification');
        }


        $secret = $this->container->settings['secret'];
        $token = JWT::encode(['iss' => 'http://docketu.iutnc.univ-lorraine.fr:62011/auth',
            'aud' => 'http://docketu.iutnc.univ-lorraine.fr:62016',
            'iat' => time(),
            'exp' => time() + (3600 * 24 * 30), // validité = 30 jours
            'upr' => [
                'user_id' => $user->id,
                'user_fullname' => $user->fullname,
                'user_email' => $user->email,
                'user_username' => $user->username,
                'user_isAdmin' => $user->is_admin,
            ]],
            $secret, 'HS256');

        $user->refresh_token = $token;
        $user->save();

        //return the refresh_token
        $data = [
            'refresh-token' => $user->refresh_token,
        ];

        return Writer::json_output($resp, 200, $data);


    }


    public function check(Request $req, Response $resp, $args): Response {
        try {
            //le secret est conservé dans le container de dépendances
            $secret = $this->container->settings['secret'];
            $secret_guest = $this->container->settings['secret_guest'];

            //le token est récupéré et scanné depuis le header "Authorization" de la requête
            $h = $req->getHeader('Authorization')[0] ;
            $tokenstring = sscanf($h, "Bearer %s")[0] ;
            try {
                $token = JWT::decode($tokenstring, new Key($secret,'HS256' ) );
            } catch (\Throwable $th) {
                $token = JWT::decode($tokenstring, new Key($secret_guest,'HS256' ) );
            }


            return Writer::json_output($resp, 200, "Token is verified");
        } 
        catch (ExpiredException $e) {
            return Writer::jsonError($req, $resp, 401, 'The token is expired');

        } catch (SignatureInvalidException $e) {
            return Writer::jsonError($req, $resp, 401, 'The signature isn\'t valid');

        } catch (BeforeValidException $e) {
            return Writer::jsonError($req, $resp, 401, 'BeforeValidException');

        } catch (\UnexpectedValueException $e) { 
            return Writer::jsonError($req, $resp, 401, 'The value of token is wrong');

        }    
        return $resp;
    }


    public function createAccount(Request $req, Response $resp, $args): Response {
        //get body of request
        $requestBody = $req->getParsedBody();
        //get the UUID from middleware
        $idUser = $req->getAttribute('idUser');

        //if condition about validators filter
        if ($req->getAttribute('has_errors')) {
            $errors = $req->getAttribute('errors');
            var_dump($errors);
        } else {
            try {
                if ($requestBody['password'] !== $requestBody['password_confirm']) {
                    return Writer::jsonError($req, $resp, 401, 'Les mots de passes ne sont pas identiques');
                }
                else {

                    $checkEmail = User::select('username', 'email')->get();
                        
                    foreach ($checkEmail as $i) {
                        if($requestBody['email'] !== $i['email'] && $requestBody['username'] !== $i['username']){
                            continue;
                        }
                        else{
                            return Writer::jsonError($req, $resp, 401, 'Cet email ou pseudo existe déjà !');
                        }
                    }
                    //creation of a new user
                    $newUser = new User();
                    $newUser->id = $idUser;
                    $newUser->fullname = $requestBody['fullname'];
                    $newUser->email = $requestBody['email'];
                    $newUser->username = $requestBody['username'];
                    $newUser->password = password_hash($requestBody['password'], PASSWORD_DEFAULT);
                    $newUser->is_admin = 0;
                    $newUser->save();
                }    
            } catch (ModelNotFoundException $e) {
                $resp = $resp->withHeader('WWW-authenticate', 'Basic realm="reunionou auth" ');
                return Writer::jsonError($req, $resp, 401, 'Erreur authentification model');
            } catch (\Exception $e) {
                $resp = $resp->withHeader('WWW-authenticate', 'Basic realm="reunionou auth" ');
                return Writer::jsonError($req, $resp, 401, 'Erreur PHP');
            }    
            
            //configure the response headers
            $resp = $resp->withStatus(201)
                        ->withHeader('Content-Type', 'application/json; charset=utf-8');
            
            return Writer::json_output($resp, 200, 'Successful creation !');

            return $resp;
        }
    }


    public function updateAccount(Request $req, Response $resp, $args): Response {    
        //get body of request
        $requestBody = $req->getParsedBody();
        // old, new & new confirm
        $userID = $args['id'];

        //if condition about validators filter
        if ($req->getAttribute('has_errors')) {
            $errors = $req->getAttribute('errors');
            var_dump($errors);
        } else {
            try {
                $user = User::findOrFail($userID);

                if (!password_verify($requestBody['old_password'], $user->password)){
                    return Writer::jsonError($req, $resp, 401, 'L\'ancien mot de passe ne correpsond pas');
                }
                else if ($requestBody['new_password'] !== $requestBody['new_password_confirm']) {
                    return Writer::jsonError($req, $resp, 401, 'Les mots de passes ne sont pas identiques');
                }
                else {
                    $checkEmail = User::select('username', 'email')->where('id', '!=', $user->id)->get();
                        
                    foreach ($checkEmail as $i) {
                        if($requestBody['email'] !== $i['email'] && $requestBody['username'] !== $i['username']){
                            continue;
                        }
                        else{
                            return Writer::jsonError($req, $resp, 401, 'Cet email ou pseudo existe déjà !');
                        }
                    }
                    if($requestBody['new_password']!=""){
                        $user->password = password_hash($requestBody['new_password'], PASSWORD_DEFAULT);
                    }
                    $user->fullname = $requestBody['fullname'];
                    $user->email = $requestBody['email'];
                    $user->username = $requestBody['username'];

                    $secret = $this->container->settings['secret'];
                    $token = JWT::encode(['iss' => 'http://docketu.iutnc.univ-lorraine.fr:62011/auth',
                        'aud' => 'http://docketu.iutnc.univ-lorraine.fr:62014',
                        'iat' => time(),
                        'exp' => time() + (3600 * 24 * 30), // validité = 30 jours
                        'upr' => [
                            'user_id' => $user->id,
                            'user_fullname' => $user->fullname,
                            'user_email' => $user->email,
                            'user_username' => $user->username,
                            'user_isAdmin' => $user->is_admin,
                        ]],
                        $secret, 'HS512');

                    
                    $user->refresh_token = $token;
                    $user->save();

                    return Writer::json_output($resp, 200, 'Successful update !');
                }    
            } catch (ModelNotFoundException $e) {
                $resp = $resp->withHeader('WWW-authenticate', 'Basic realm="reunionou auth" ');
                return Writer::jsonError($req, $resp, 401, 'Erreur authentification model');
            } catch (\Exception $e) {
                $resp = $resp->withHeader('WWW-authenticate', 'Basic realm="reunionou auth" ');
                return Writer::jsonError($req, $resp, 401, 'Erreur PHP');
            }    
        }

    }
    
    public function getUsers(Request $req, Response $resp, $args): Response {
        try { 
            $allUsers = User::select(['id', 'fullname', 'username'])->get();
            $count = count($allUsers);
            $linkUsers = $this->container->router->pathFor('getUsers');
            
            $users_resp = [];
            foreach ($allUsers as $u) {
                $users_resp[] = [
                    'user_id' => $u->id,
                    'user_fullname' => $u->fullname,
                    'user_username' => $u->username,
                ];
            }

            $data = [
                "type" => "collection",
                "count" => $count,
                "users" => $users_resp,
                "links" => [
                    "self" => [
                        "href" => $linkUsers
                    ]
                ]
            ];

            return Writer::json_output($resp, 200, $data);

        } catch (ModelNotFoundException $e) {
            $resp = $resp->withHeader('WWW-authenticate', 'Basic realm="reunionou auth" ');
            return Writer::jsonError($req, $resp, 401, 'Erreur model');
        } catch (\Exception $e) {
            $resp = $resp->withHeader('WWW-authenticate', 'Basic realm="reunionou auth" ');
            return Writer::jsonError($req, $resp, 401, 'Erreur PHP');
        }
    }


    public function getUserByID(Request $req, Response $resp, $args): Response {
        $userID = $args['id'];
        try { 
            $user = User::select(['id', 'fullname','email', 'username', 'refresh_token'])->findOrFail($userID);
            $linkUsers = $this->container->router->pathFor('getUsers');
            $linkSelfUser = $this->container->router->pathFor('getUserByID',['id' => $user->id]);


            $data = [
                "type" => "ressouce",
                "user" => $user,
                "links" => [
                    "users" => [
                        "href" => $linkUsers
                    ],
                    "self" => [
                        "href" => $linkSelfUser
                    ]
                ],
            ];

            return Writer::json_output($resp, 200, $data);

        } catch (ModelNotFoundException $e) {
            $resp = $resp->withHeader('WWW-authenticate', 'Basic realm="reunionou auth" ');
            return Writer::jsonError($req, $resp, 401, 'Erreur authentification model');
        } catch (\Exception $e) {
            $resp = $resp->withHeader('WWW-authenticate', 'Basic realm="reunionou auth" ');
            return Writer::jsonError($req, $resp, 401, 'Erreur PHP');
        }
    }


    public function checkAdmin(Request $req, Response $resp, $args): Response {

        if (!$req->hasHeader('Authorization')) {

            $resp = $resp->withHeader('WWW-authenticate', 'Basic realm="commande_api api" ');
            return Writer::jsonError($req, $resp, 401, 'No Authorization header present');
        };

        $authstring = base64_decode(explode(" ", $req->getHeader('Authorization')[0])[1]);
        list($email, $pass) = explode(':', $authstring);

        try {
            $admin = User::select('is_Admin')->where('email', '=', $email)->where('is_Admin', '=', 1)->firstOrFail();
            return Writer::json_output($resp, 200, $admin);

        } catch (ModelNotFoundException $e) {
            $resp = $resp->withHeader('WWW-authenticate', 'Basic realm="reunionou auth" ');
            return Writer::jsonError($req, $resp, 401, 'Erreur authentification model');
        } catch (\Exception $e) {
            $resp = $resp->withHeader('WWW-authenticate', 'Basic realm="reunionou auth" ');
            return Writer::jsonError($req, $resp, 500, 'Erreur PHP');
        }
    }
}