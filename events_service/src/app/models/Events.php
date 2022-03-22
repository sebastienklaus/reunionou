<?php

namespace reu\events\app\models;

class Events extends \Illuminate\Database\Eloquent\Model
{

    protected $table      = 'events';  /* le nom de la table */
    protected $primaryKey = 'id';

    public  $incrementing = false;      //pour primarykey, on annule l'auto_increment
    public $keyType = 'string';

    // fillable ?

    public function messages()
    {
        return $this->hasMany('\reu\events\app\models\Messages', 'event_id');
    }

    public function members()
    {
        return $this->hasMany('\reu\events\app\models\Members', 'event_id');
    }
}
