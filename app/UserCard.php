<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserCard extends Model
{
    // basic relation to users 

    public function userDetails() {

    	return $this->belongsTo(User::class);
    	
    }
}
