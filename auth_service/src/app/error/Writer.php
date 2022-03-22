<?php

namespace reunionou\auth\app\error;

use \Psr\Http\Message\ServerRequestInterface as Request ;
use \Psr\Http\Message\ResponseInterface as Response ;
use Psr\Container\ContainerInterface;


class Writer extends \Exception{

    public static function jsonError (Request $req, Response $resp, int $error, string $msg){
        //complete the data array
        $data = [
            'type' => 'error',
            'error' => $error,
            'message' => $msg
        ];

        //configure the response headers
        $resp = $resp->withStatus($error)
                    ->withHeader('Content-Type', 'application/json; charset=utf-8');

        //write in the body with data encode with a json_encode function
        $resp->getBody()->write(json_encode($data));

        //return the response (ALWAYS !)
        return $resp;
    }


    public static function json_output(Response $resp, int $code_resp, $data): Response{
        $resp = $resp->withStatus($code_resp)
                    ->withHeader('Content-Type', 'application/json; charset=utf-8');
        
        $resp->getBody()->write(json_encode($data));
    
        return $resp;
    }
}