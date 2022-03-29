<?php

namespace reu\events\app\models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class Members extends \Illuminate\Database\Eloquent\Model
{

    protected $table      = 'members';  /* le nom de la table */
    protected $primaryKey = 'id';

    public  $incrementing = false;      //pour primarykey, on annule l'auto_increment
    public $timestamps = true;

    // protected $fillable = array(
    //     'id', 'uri', 'libelle', 'tarif', 'quantite', 'command_id'
    // );


    public function event()
    {
        return $this->belongsTo('\reu\events\app\models\Events', 'event_id');
    }

    public function messages()
    {
        return $this->hasMany('\reu\events\app\models\Messages', 'member_id');
    }
}
