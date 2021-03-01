<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    /** Relations with User */
    public function Sender()
    {
        return $this->belongsTo('App\User', 'from');
    }
    public function Reciever()
    {
        return $this->belongsTo('App\User', 'to');
    }
}
