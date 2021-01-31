<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    /**
     * Scope a query to only include active users.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCommonResponse($query) {

        return $query->select(
            'categories.id as category_id',
            'categories.name as category_user_display_name',
            'categories.provider_name as category_provider_display_name',
            'categories.picture as picture',
            'categories.description as description',
            'categories.created_at',
            'categories.updated_at'
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
            'categories.id as category_id',
            'categories.id as api_page_type_id',
            'categories.name as name',
            'categories.picture as picture',
            'categories.description as description',
            'categories.provider_name as category_provider_display_name',
            'categories.created_at',
            'categories.updated_at'
            );
    
    }

    public function subCategories() {

        return $this->hasMany(SubCategory::class);
    }

    public function approvedSubCategories() {

        return $this->hasMany(SubCategory::class, 'category_id')->where('sub_categories.status', APPROVED);
    }

    public function commonQuestions() {

        return $this->hasMany(CommonQuestion::class);
    }

    public function commonQuestionAnswers() {

        return $this->hasMany(CommonQuestionAnswer::class);
    }

    public function verifiedHosts() {

        return $this->hasMany(Host::class, 'category_id')
                    ->where('hosts.status' , HOST_OWNER_PUBLISHED)
                    ->where('hosts.admin_status' , ADMIN_HOST_APPROVED)
                    ->where('hosts.is_admin_verified' , ADMIN_HOST_VERIFIED);
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

            $model->subCategories()->delete();

            $model->commonQuestions()->delete();

        });

    }
}
