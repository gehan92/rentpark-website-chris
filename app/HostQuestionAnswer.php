<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HostQuestionAnswer extends Model
{
    /**
     * Get the cards record associated with the provider.
     */
    public function commonQuestionDetails() {
        
        return $this->belongsTo(CommonQuestion::class, 'common_question_id');
    }

    /**
     * Scope a query to only include active users.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUserAmentiesResponse($query, $type = QUESTION_TYPE_AMENTIES) {
        
       
        $type = $type ?? QUESTION_TYPE_AMENTIES;
        
    	$query = $query->leftJoin('common_questions', 'common_questions.id', '=', 'host_question_answers.common_question_id')
	    		->where('question_static_type', $type)
	    		->select('user_question', 'common_question_id','question_input_type','host_question_answers.common_question_answer_id', 'host_question_answers.answers');
        
    	return $query;

    }

    public function scopeUserSearchAmenities($query) {

        $query = $query->leftJoin('common_questions', 'common_questions.id', '=', 'host_question_answers.common_question_id')
                        ->leftJoin('hosts', 'hosts.id', '=', 'host_question_answers.host_id')
                        ->where('hosts.status' , HOST_OWNER_PUBLISHED)
                        ->where('hosts.admin_status' , ADMIN_HOST_APPROVED)
                        ->where('hosts.is_admin_verified' , ADMIN_HOST_VERIFIED)
                        ->where('common_questions.question_static_type', QUESTION_TYPE_AMENTIES);


        return $query;
    }


}
