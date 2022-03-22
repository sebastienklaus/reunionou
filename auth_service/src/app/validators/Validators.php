<?php

namespace reunionou\auth\app\validators;

use \Respect\Validation\Validator as v;

class Validators{

    public static function validators_createUser(){

        return [
            'fullname' => v::stringType()->alpha(),
            'email' => v::email(),
            'username' => v::stringType()->alnum('@ - . + _'),
            'password'=> v::stringType()->alnum("! # $ % & ' ( ) * + , - . / : ; < = > ? @ [ \ ] ^ _ ` { | } ~ "),
        ];
    }
}