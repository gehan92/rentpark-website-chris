<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{

    /**
     * Scope a query to only include active users.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCommonResponse($query) {


        return $query->leftJoin('categories', 'sub_categories.category_id', '=','categories.id')
            ->select(
                'categories.id as category_id',
                'categories.name as category_user_display_name',
                'categories.provider_name as category_provider_display_name',
                'sub_categories.id as sub_category_id',
                'sub_categories.name as sub_category_user_display_name',
                'sub_categories.provider_name as sub_category_provider_display_name',
                'sub_categories.picture as picture',
                'sub_categories.description as description',
                'sub_categories.created_at',
                'sub_categories.updated_at'
            );
    
    }

    /**
     * Scope a query to only include active users.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeHomeResponse($query) {
        
        return $query->has('verifiedHosts')
            ->select(
                'sub_categories.id as sub_category_id',
                'sub_categories.id as api_page_type_id',
                'sub_categories.name as name',
                'sub_categories.picture as picture',
                'sub_categories.description as description',
                'sub_categories.provider_name as sub_category_provider_display_name',
                'sub_categories.created_at',
                'sub_categories.updated_at'
            );
    
    }

    public function verifiedHosts() {

        return $this->hasMany(Host::class, 'category_id')
                    ->where('hosts.status' , HOST_OWNER_PUBLISHED)
                    ->where('hosts.admin_status' , ADMIN_HOST_APPROVED)
                    ->where('hosts.is_admin_verified' , ADMIN_HOST_VERIFIED);
    }


	/**
     * Get the category details.
     */
    public function categoryDetails() {
        
        return $this->hasMany(Category::class);
    }

    /**
     * Get the category details.
     */
    public function hosts() {
        
        return $this->hasMany(Host::class);
    }

    public static function boot() {

        parent::boot();

        static::creating(function ($model) {

        	$model->attributes['unique_id'] = uniqid();

        });
        
        static::created(function ($model) {

            $model->attributes['unique_id'] = "CID"."-".$model->attributes['id']."-".uniqid();

        });

        static::deleting(function ($model) {

            $model->hosts()->delete();

        });


    }
}
