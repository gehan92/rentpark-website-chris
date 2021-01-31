<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Lookups extends Model
{
    /**
     * Get the Approved Lookups details 
     */
    public function scopeApproved($query) {
        
        return $query->where('lookups.status' , APPROVED);	
    }    

    /**
     * Get the Approved Lookups details 
     */
    public function scopeareAmenities($query) {
        
        return $query->where('lookups.is_amenity' , YES);	
    }
}
