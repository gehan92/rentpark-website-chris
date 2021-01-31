<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HostAvailabilityList extends Model
{
    /**
     * Scope a query to only include active users.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCommonResponse($query) {

    	return $query->select('id as host_availability_id',
    					'from_date', 
    					'to_date', 
    					'spaces', 
    					'type');

    }
}
