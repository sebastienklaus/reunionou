<?php

namespace reu\events\app\middleware;

use  \Respect\Validation\Validator as V;

class EventValidator
{

    public static function create_validators()
    {
        // Règles de validations :
        // return [
        //     'nom' => V::StringType()->alpha(),
        //     'mail' => V::email(),
        //     'livraison' => [
        //         'date' => V::date('d-m-Y')->min('now'),
        //         'heure' => V::date('H:i')
        //     ],
        //     'items' => V::arrayVal()->each(
        //         V::arrayVal()
        //             ->key('uri', V::StringType())
        //             ->key('q', V::intVal())
        //             ->key('libelle', V::StringType())
        //             ->key('tarif', V::floatVal()),
        //     )
        //     // 'items' => v::arrayType()->each(
        //     //     // v::key('q', v::numeric()),
        //     //     // v::key('libelle', v::StringType()->alpha()),
        //     //     // v::key('tarif', v::floatType()),
        //     //     v::arrayVal()
        //     //         ->key('uri', v::stringType()->alnum('/'))
        //     //         ->key('q', v::intType()->positive())
        //     //         ->key('libelle', v::stringType()->alpha())
        //     //         ->key('tarif', v::floatType()),

        //     // ),
        // ];

        return [
            'title' => v::stringType()->notEmpty(),
            'description' => v::stringType()->notEmpty(),
            'author' => v::noWhitespace()->length(1, 36),
            // 'date' => [
            //     'date'=> V::date('d-m-Y')->min('now'),
            //     'heure' => V::date('H:i')
            // ],
            'date' => V::date('d-m-Y H:i'),
            'lieu' => v::stringType()->notEmpty(),
        ];
    }
}
