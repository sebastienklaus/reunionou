<?php

namespace reu\events\app\models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class Item extends \Illuminate\Database\Eloquent\Model
{

    protected $table      = 'item';  /* le nom de la table */
    protected $primaryKey = 'id';

    public  $incrementing = true;      //pour primarykey, on annule l'auto_increment
    public $timestamps = true;

    // protected $fillable = array(
    //     'id', 'uri', 'libelle', 'tarif', 'quantite', 'command_id'
    // );



    public function events()
    {
        return $this->belongsTo('\reu\events\app\models\Events', 'command_id');
    }
}
