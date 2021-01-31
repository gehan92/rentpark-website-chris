<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserVehicle extends Model
{
    //

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'vehicle_type', 'vehicle_number', 'vehicle_brand','vehicle_model'
    ]; 

    /**
     * Scope a query to only include active users.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCommonResponse($query) {

        return $query->select(
            'user_vehicles.id as user_vehicle_id',
            'user_vehicles.vehicle_type',
            'user_vehicles.vehicle_number',
            'user_vehicles.vehicle_brand',
            'user_vehicles.vehicle_model',
            'user_vehicles.created_at',
            'user_vehicles.updated_at'
            );
    
    }
}
