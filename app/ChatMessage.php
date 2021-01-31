<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
       'host_id', 'booking_id', 'provider_id','user_id','message', 'type', 'status'
    ];
}
