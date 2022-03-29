<?php

namespace reu\events\app\models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class Messages extends \Illuminate\Database\Eloquent\Model
{

    protected $table      = 'messages';  /* le nom de la table */
    protected $primaryKey = 'id';

    public  $incrementing = false;      //pour primarykey, on annule l'auto_increment
    public $timestamps = true;

    protected $fillable = array(
        'id', 'content', 'member_id', 'event_id', 'media', 'created_at', 'updated_at'
    );



    public function event()
    {
        return $this->belongsTo('\reu\events\app\models\Events', 'event_id');
    }

    public function member()
    {
        return $this->belongsTo('\reu\events\app\models\Members', 'member_id');
    }
}
