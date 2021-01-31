<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserRefund extends Model
{
    /**
     * Get the provider details associated with booking
     */
    public function userDetails() {

        return $this->belongsTo(User::class, 'user_id');

    }
}
