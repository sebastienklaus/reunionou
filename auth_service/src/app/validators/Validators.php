<?php

namespace reunionou\auth\app\validators;

use \Respect\Validation\Validator as v;

class Validators{

    public static function validators_createUser(){
        
        $validators = [
            'fullname' => v::stringType()->alpha(),
            'email' => v::email(),
            'username' => v::stringType()->alnum('@ - . + _'),
            'password'=> v::stringType()->alnum("! # $ % & ' ( ) * + , - . / : ; < = > ? @ [ \ ] ^ _ ` { | } ~ ")->length(8, 64),
        ];

        return $validators;
    }

    public static function validators_updateUser(){
        
        $validators = [
            'fullname' => v::stringType()->alpha(),
            'email' => v::email(),
            'username' => v::stringType()->alnum('@ - . + _'),
            'new_password'=> v::stringType()->alnum("! # $ % & ' ( ) * + , - . / : ; < = > ? @ [ \ ] ^ _ ` { | } ~ ")->length(8, 64),
        ];

        return $validators;
    }
}