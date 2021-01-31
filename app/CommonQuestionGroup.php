<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CommonQuestionGroup extends Model
{
    public static function boot() {

        parent::boot();

        static::creating(function ($model) {

        	$model->attributes['unique_id'] = uniqid();

        });
        
        static::created(function ($model) {

            $model->attributes['unique_id'] = "CID"."-".$model->attributes['id']."-".uniqid();

        });

    }
}
