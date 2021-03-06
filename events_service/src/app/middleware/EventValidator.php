<?php

namespace reu\events\app\middleware;

use  \Respect\Validation\Validator as V;

class EventValidator
{

    public static function create_validators()
    {

        return [
            'title' => v::stringType()->notEmpty()->alpha(" ' é è à ç ù ê î"),
            'description' => v::stringType()->notEmpty()->alpha(" ' é è à ç ù ê î"),
            'user_id' => v::noWhitespace()->length(1, 64),
            'location' => v::json()->notEmpty(),
            'date' => V::date('Y-m-d')->min('now'),
            'heure' => V::date('H:i')
        ];
    }
}

