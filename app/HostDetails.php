<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HostDetails extends Model
{
    
    public function host() {
    	return $this->belongsTo('App\Host');
    }
}
