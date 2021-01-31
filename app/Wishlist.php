<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    /**
     * Scope a query to only include active users.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCommonResponse($query) {

        return $query->leftJoin('hosts', 'hosts.id', '=', 'wishlists.host_id')->leftJoin('users', 'users.id', '=', 'wishlists.user_id')
            ->where('hosts.status' , HOST_OWNER_PUBLISHED)
            ->where('hosts.admin_status' , ADMIN_HOST_APPROVED)
            ->where('hosts.is_admin_verified' , ADMIN_HOST_VERIFIED)
            ->select(
        	'wishlists.id as wishlist_id',
            	'hosts.id as host_id',
                'hosts.host_name',
            	'hosts.picture as host_picture',
                'hosts.per_day as per_day',
                'hosts.per_hour as per_hour',
                'hosts.city as host_location',
                'hosts.overall_ratings',
                'hosts.total_ratings',
                'users.id as user_id',
                'users.name as username',
                'users.picture as picture',
                'wishlists.created_at',
                'wishlists.updated_at'
            );
    
    }

    /**
     * Get the user details associated with wishlist
     */
    public function userDetails() {

        return $this->belongsTo(User::class, 'user_id');

    }

    /**
     * Get the host details associated with wishlist
     */
    public function hostDetails() {

        return $this->belongsTo(Host::class , 'host_id');
    }

}
