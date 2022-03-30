<?php

namespace reu\events\app\middleware;

use  \Respect\Validation\Validator as V;

class MemberValidator
{

    public static function create_validators()
    {
        return [
            'user_id' => v::noWhitespace()->length(0,64),
            'event_id' => v::stringType()->notEmpty()->noWhitespace(),
            'pseudo' => v::stringType()->notEmpty()->alpha(" ' é è à ç ù ê î"),
        ];
    }
}


