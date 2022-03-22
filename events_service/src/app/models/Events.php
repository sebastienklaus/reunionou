<?php

namespace reu\events\app\models;

class Commande extends \Illuminate\Database\Eloquent\Model
{

    protected $table      = 'commande';  /* le nom de la table */
    protected $primaryKey = 'id';

    public  $incrementing = false;      //pour primarykey, on annule l'auto_increment
    public $keyType = 'string';

    // fillable ?

    public function items()
    {
        return $this->hasMany('\reu\events\app\models\Item', 'command_id');
    }
}
