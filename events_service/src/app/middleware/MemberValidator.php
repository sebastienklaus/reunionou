<?php

namespace reu\events\app\middleware;

use  \Respect\Validation\Validator as V;

class MessageValidator
{

    public static function create_validators()
    {
        return [
            'id' => v::stringType()->notEmpty(),
            'content' => v::stringType()->notEmpty(),
            'member_id' => v::noWhitespace()->length(1, 36),
            'event_id' => v::stringType()->notEmpty()->noWhitespace(),
            'media' => v::stringType()
        ];
    }
}


