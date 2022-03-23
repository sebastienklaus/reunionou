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
            'event_id' => v::stringType()->notEmpty(),
            'media' => v::json()->validate(),
            'created_at' => V::date('d-m-Y H:i'),
            'updated_at' => V::date('d-m-Y H:i')
        ];

        //Erreur retournées

        if ($req->getAttribute('has_errors')) {

            $errors = $req->getAttribute('errors');

            if (isset($errors['content'])) {
                $this->container->get('logger.error')->error("error input event content");
                return Writer::json_error($resp, 403, '"content" : invalid input, string expected');
            }
            if (isset($errors['member_id'])) {
                $this->container->get('logger.error')->error("error input event member_id");
                return Writer::json_error($resp, 403, '"member_id" : invalid input, valid member_id expected');
            }
            if (isset($errors['event_id'])) {
                $this->container->get('logger.error')->error("error input event event_id");
                return Writer::json_error($resp, 403, '"event_id" : invalid input, string expected');
            }
            if (isset($errors['media'])) {
                $this->container->get('logger.error')->error("error input event media");
                return Writer::json_error($resp, 403, '"media" : invalid input, valid JSON expected');
            }
            if (isset($errors['created_at'])) {
                $this->container->get('logger.error')->error("error input event created_at");
                return Writer::json_error($resp, 403, '"created_at" : invalid input. date exepected : d-m-y H:m:i');
            }
            if (isset($errors['updated_at'])) {
                $this->container->get('logger.error')->error("error input event updated_at");
                return Writer::json_error($resp, 403, '"updated_at" : invalid input. date exepected : d-m-y H:m:i');
            }
            
        };
    }
}