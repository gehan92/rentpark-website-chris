<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StaticPage extends Model
{
    public static function boot() {

        parent::boot();

        static::creating(function ($model) {

            $model->attributes['unique_id'] = routefreestring(isset($model->attributes['title']) ? $model->attributes['title'] : uniqid());

        });
        
        static::updating(function($model) {

            $model->attributes['unique_id'] = routefreestring(isset($model->attributes['title']) ? $model->attributes['title'] : uniqid());

        });
 
    }
}
