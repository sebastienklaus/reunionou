<?php

namespace reunionou\auth\app\model;


use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Psr\Container\ContainerInterface;

class User extends \Illuminate\Database\Eloquent\Model
{

    protected $table      = 'users';  /* le nom de la table */
    protected $primaryKey = 'id';
    public    $timestamps = true;    /* si vrai la table doit contenir
                                        les deux colonnes updated_at,
                                        created_at */

    protected $fillable = array(
        'id', 'fullname', 'email', 'username', 'password', 'refresh_token', 'created_at', 'updated_at', 'is_admin'
    );


    public  $incrementing = false;
    public $keyType = 'string';
}
