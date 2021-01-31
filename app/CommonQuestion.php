<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CommonQuestion extends Model
{
	/**
     * Get the category details record associated with the CommonQuestion.
     */
    public function categoryDetails() {
		return $this->belongsTo(Category::class , 'category_id');
	}
    
    /**
     * Get the subCategory details record associated with the CommonQuestion.
     */
    public function subCategoryDetails() {
        return $this->belongsTo(SubCategory::class , 'sub_category_id');
    }	

    /**
     * Get the commonQuestionGroup details record associated with the CommonQuestion.
     */
    public function commonQuestionGroupDetails() {
		return $this->belongsTo(CommonQuestionGroup::class , 'common_question_group_id');
	}

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
