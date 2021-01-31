<?php

namespace App\Repositories;

use App\Helpers\Helper;

use App\Helpers\HostHelper;

use DB, Log, Validator, Exception, Setting;

use App\User;

use App\Category, App\SubCategory;

use App\Host, App\HostGallery, App\HostDetails;

use App\ServiceLocation;

use App\CommonQuestion, App\CommonQuestionAnswer;

use App\HostQuestionAnswer, App\HostAvailability;

class HostRepository {

    /**
     *
     * @method host_list_response()
     *
     * @uses used to get the common list details for hosts
     *
     * @created Vidhya R
     *
     * @updated Vidhya R
     *
     * @param array $host_ids
     *
     * @param integer $user_id
     *
     * @return
     */

    public static function host_list_response($host_ids, $user_id) {

        $hosts = Host::whereIn('hosts.id' , $host_ids)
                            ->orderBy('hosts.updated_at' , 'desc')
                            ->UserBaseResponse()
                            ->get();

        foreach ($hosts as $key => $host_details) {

            $host_details->wishlist_status = NO;

            if($user_id) {

                $host_details->wishlist_status = HostHelper::wishlist_status($host_details->host_id, $user_id);

            }

            $host_details->host_location = $host_details->serviceLocationDetails->name ?? $host_details->host_location;

            $host_details->base_price_formatted = formatted_amount($host_details->base_price);
            
            $host_details->per_day_formatted = formatted_amount($host_details->per_day);

            $host_details->per_day_symbol = tr('list_per_day_symbol');

            $host_details->per_hour_formatted = formatted_amount($host_details->per_hour);

            $host_details->per_hour_symbol = tr('list_per_hour_symbol');

            $host_galleries = HostGallery::where('host_id', $host_details->host_id)->select('picture', 'caption')->skip(0)->take(3)->get();

            $host_details->gallery = $host_galleries;
        }

        return $hosts;

    } 

    /**
     *
     * @method park_hosts_list_response()
     *
     * @uses used to get the common list details for hosts
     *
     * @created Vidhya R
     *
     * @updated Vidhya R
     *
     * @param array $host_ids
     *
     * @param integer $user_id
     *
     * @return
     */

    public static function park_hosts_list_response($host_ids, $user_id, $request = []) {

        $hosts = Host::whereIn('hosts.id' , $host_ids)
                            ->orderBy('hosts.updated_at' , 'desc')
                            ->UserParkResponse()
                            ->get();

        foreach ($hosts as $key => $host_details) {

            $host_details->total_distance = calculate_distance($host_details->latitude, $host_details->longitude, $request->latitude, $request->longitude);

            $host_details->wishlist_status = NO;

            if($user_id) {

                $host_details->wishlist_status = HostHelper::wishlist_status($host_details->host_id, $user_id);

            }
            
            $host_details->per_hour_formatted = formatted_amount($host_details->per_hour);

            $host_details->per_hour_symbol = tr('list_per_hour_symbol');

            $host_galleries = HostGallery::where('host_id', $host_details->host_id)->select('picture', 'caption')->skip(0)->take(3)->get();

            $host_details->gallery = $host_galleries;

            unset($host_details->serviceLocationDetails);
        }

        return $hosts;

    } 

    /**
     *
     * @method host_steps_list()
     *
     * @uses used to get the common list details for hosts
     *
     * @created Vidhya R
     *
     * @updated Vidhya R
     *
     * @param 
     *
     * @return
     */

    public static function host_steps_list($request) {

        $steps = [HOST_STEP_1 => tr('HOST_STEP_1'), HOST_STEP_2 => tr('HOST_STEP_2'), HOST_STEP_3 => tr('HOST_STEP_3'), HOST_STEP_4 => tr('HOST_STEP_4'), HOST_STEP_5 => tr('HOST_STEP_5'), HOST_STEP_6 => tr('HOST_STEP_6'), HOST_STEP_7 => tr('HOST_STEP_7'), HOST_STEP_8 => tr('HOST_STEP_8')];

        $host_step = HOST_STEP_0;

        $host_steps = [];

        if($request->host_id) {

            $host_details = HostDetails::where('host_id', $request->host_id)->select('step1', 'step2', 'step3', 'step4', 'step5', 'step6', 'step7', 'step8')->first();

            $host_steps = $host_details ? array_values($host_details->toArray()) : [];

            $host_step = $host_details ? $host_details->step : HOST_STEP_1;
        }

        $check_step_index = 0;

        foreach ($steps as $key => $step_details) {

            $step_data = [];

            $step_data['title'] = $step_details;

            $step_data['description'] = "";

            $step_data['step'] = $key;

            $step_data['is_step_completed'] = $host_steps ? $host_steps[$check_step_index] : NO;

            $data[] = $step_data;

            $check_step_index++;
        }

        return $data;
    
    }

    /**
     *
     * @method host_step_1()
     *
     * @uses used to get the common list details for hosts
     *
     * @created Vidhya R
     *
     * @updated Vidhya R
     *
     * @param 
     *
     * @return
     */

    public static function host_step_1($request) {

        $host = $host_details = [];

        if($request->host_id) {

            $host = Host::where('provider_id', $request->id)->where('id', $request->host_id)->first();

            $host_details = HostDetails::where('host_id', $request->host_id)->where('provider_id', $request->id)->first();

        }

        $data = [];

        /* * * * * * static section start * * * * * * * */

        $static_page = new \stdClass;

        $static_page->title = tr('api_category_choose_title');

        $static_page->description = tr('api_category_choose_description');

        $static_page_questions_data = [];

        // Choose category question

        $static_page_questions = [];

        $static_page_questions['question'] = tr('api_category_choose_question');

        $static_page_questions['type'] = ABOUT_HOST_SPACE;

        $static_page_questions['sub_type'] = SPINNER_CALL_SUB_CATEGORY;

        $static_page_questions['sub_type_server_key'] = 'sub_category_id';

        $static_page_questions['server_key'] = "category_id";

        $static_page_questions['is_required'] = YES;

        $categories = Category::where('categories.status' , APPROVED)->orderBy('name' , 'asc')->select('id as key', 'provider_name as value')->get();

        foreach ($categories as $key => $category_details) {

            // * * *  Check the answer is already marked start * * * //

            $is_checked = NO;

            if($request->host_id && $host) {

                $is_checked = $host->category_id == $category_details->key ? YES : NO;

            }

            $category_details->is_checked = $is_checked;

            // * * *  Check the answer is already marked end * * * //

            $category_details->is_required = YES;

            $category_details->picture = $category_details->description = "";
            
        }

        $static_page_questions['answer'] = $categories;

        $sub_answers = [];

        if($request->host_id && $host) {

            $sub_answers = SubCategory::where('category_id', $host->category_id)
                                    ->CommonResponse()
                                    ->where('sub_categories.status', APPROVED)
                                    ->orderBy('sub_categories.name' , 'asc')
                                    ->get();

            foreach ($sub_answers as $key => $sub_category_details) {

                $sub_category_details->is_checked  = $host->sub_category_id == $sub_category_details->sub_category_id ? YES : NO;

            }
        }

        $static_page_questions['sub_answer'] = $sub_answers;

        array_push($static_page_questions_data, $static_page_questions);

        // Choose category question end 

        // Choose host type question

        $static_page_questions = [];

        $static_page_questions['question'] = tr('api_host_type_title');

        $static_page_questions['type'] = RADIO;

        $static_page_questions['server_key'] = "host_type";

        $static_page_questions['is_required'] = YES;

        $static_page_questions['answer'] = Helper::get_provider_host_types($host);

        array_push($static_page_questions_data, $static_page_questions);

        // Choose host type question end 


        $static_page->data = $static_page_questions_data;

        array_push($data, $static_page);

        /* * * * * * static section end * * * * * * * */

        /* * * * * * Section 1 start * * * * * * * */

        $page1 = new \stdClass;

        $page1->title = tr('api_guests_page_title');

        $page1->description = tr('api_guests_page_description');

        $page1_questions_data = [];

        // $page1_questions = [];

        // $page1_questions['question'] = tr('api_no_of_guests_question');

        // $page1_questions['type'] = INCREMENT_DECREMENT;

        // $page1_questions['server_key'] = "total_guests";

        // $page1_questions['is_required'] = YES;

        // $page1_questions['answer'] = $host_details ? $host_details->total_guests : 0;

        // array_push($page1_questions_data, $page1_questions);


        $page1_questions = [];

        $page1_questions['question'] = tr('api_min_guests_question');

        $page1_questions['type'] = INCREMENT_DECREMENT;

        $page1_questions['server_key'] = "min_guests";

        $page1_questions['is_required'] = YES;

        $page1_questions['answer'] = $host_details ? $host_details->min_guests : 0;

        array_push($page1_questions_data, $page1_questions);

        $page1_questions = [];

        $page1_questions['question'] = tr('api_max_guests_question');

        $page1_questions['type'] = INCREMENT_DECREMENT;

        $page1_questions['server_key'] = "max_guests";

        $page1_questions['is_required'] = YES;

        $page1_questions['answer'] = $host_details ? $host_details->max_guests : 0;

        array_push($page1_questions_data, $page1_questions);

        $page1_questions = [];

        $page1_questions['question'] = tr('api_total_bedrooms_question');

        $page1_questions['type'] = INCREMENT_DECREMENT;

        $page1_questions['server_key'] = "total_bedrooms";

        $page1_questions['is_required'] = YES;

        $page1_questions['answer'] = $host_details ? $host_details->total_bedrooms : 0;

        array_push($page1_questions_data, $page1_questions);

        $page1_questions = [];

        $page1_questions['question'] = tr('api_total_beds_question');

        $page1_questions['type'] = INCREMENT_DECREMENT;

        $page1_questions['server_key'] = "total_beds";

        $page1_questions['is_required'] = YES;

        $page1_questions['answer'] = $host_details ? $host_details->total_beds : 0;

        array_push($page1_questions_data, $page1_questions);

        // End

        $page1->data = $page1_questions_data;

        array_push($data, $page1);

        /* * * * * * Section 1 end * * * * * * * */

        /* * * * * * Page 2 end * * * * * * * */

        $page2 = new \stdClass;

        $page2->title = tr('api_choose_bathrooms_page_title');

        $page2->description = tr('api_choose_bathrooms_page_description');

        $page2_questions = $page2_questions_data = [];

        $page2_questions['question'] = tr('api_total_bathrooms_question');

        $page2_questions['type'] = INPUT;

        $page2_questions['input_type'] = INPUT_NUMBER;

        $page2_questions['placeholder'] = "";

        $page2_questions['server_key'] = "total_bathrooms";

        $page2_questions['is_required'] = YES;

        $page2_questions['answer'] = $host_details ? $host_details->total_bathrooms : 0;

        array_push($page2_questions_data, $page2_questions);

        $page2_questions = [];

        $page2_questions['question'] = tr('api_bathroom_type_question');

        $page2_questions['type'] = RADIO;

        $page2_questions['server_key'] = "bathroom_type";

        $page2_questions['is_required'] = YES;

        $page2_answer = $page2_answer_data = [];


        // Answer start

        $page2_answer['key'] = BATHROOM_TYPE_PRIVATE;

        $page2_answer['value'] = tr('BATHROOM_TYPE_PRIVATE');

        $page2_answer['is_checked'] = $host_details ? ($host_details->bathroom_type == BATHROOM_TYPE_PRIVATE ? YES : NO ) : NO;

        $page2_answer['picture'] = $page2_answer['description'] = "";

        array_push($page2_answer_data, $page2_answer);


        $page2_answer = [];

        $page2_answer['key'] = BATHROOM_TYPE_SHARED;

        $page2_answer['value'] = tr('BATHROOM_TYPE_SHARED');

        $page2_answer['is_checked'] = $host_details ? ($host_details->bathroom_type == BATHROOM_TYPE_SHARED ? YES : NO ) : NO;

        $page2_answer['picture'] = $page2_answer['description'] = "";

        array_push($page2_answer_data, $page2_answer);

        // Answer End

        $page2_questions['answer'] = $page2_answer_data;

        array_push($page2_questions_data, $page2_questions);

        $page2->data = $page2_questions_data;

        array_push($data, $page2);


        /* * * * * * Page 2 end * * * * * * * */

        return $data;
    
    }

    /**
     *
     * @method host_step_2()
     *
     * @uses used to get the common list details for hosts
     *
     * @created Vidhya R
     *
     * @updated Vidhya R
     *
     * @param 
     *
     * @return
     */

    public static function host_step_2($request) {

        $host = $host_details = [];

        if($request->host_id) {

            $host = Host::where('provider_id', $request->id)->where('id', $request->host_id)->first();

            $host_details = HostDetails::where('host_id', $request->host_id)->where('provider_id', $request->id)->first();
        }

        $page1 = $data = [];

        $page_title = $page_description = "";

        /* * * * * * Section 1 start * * * * * * * */

        $page1 = new \stdClass;

        $page1->title = tr('api_location_page_title');

        $page1->description = tr('api_location_page_description');

        $page1_questions_data = $page1_questions = [];

        // Service Locations

        $page1_questions = [];

        $page1_questions['question'] = tr('api_service_loctaion');

        $page1_questions['type'] = SPINNER;

        $page1_questions['server_key'] = "service_location_id";

        $page1_questions['is_required'] = YES;

        $service_locations = ServiceLocation::where('status', APPROVED)->get();

        $service_locationd_data = [];

        foreach ($service_locations as $key => $service_location_details) {

            $s_l_data['key'] = $service_location_details->id;

            $s_l_data['is_checked'] = NO;

            if($request->host_id) {

                $s_l_data['is_checked'] = $host ? ($host->service_location_id == $service_location_details->id ? YES : NO) : NO;
            }

            $s_l_data['value'] = $service_location_details->name;

            $s_l_data['picture'] = $s_l_data['description'] = "";

            array_push($service_locationd_data, $s_l_data);
            
        }

        $page1_questions['answer'] = $service_locationd_data;

        array_push($page1_questions_data, $page1_questions);

        // Street

        $page1_questions = [];

        $page1_questions['question'] = tr('api_location');

        $page1_questions['description'] = "";

        $page1_questions['placeholder'] = tr('api_location_placeholder');

        $page1_questions['type'] = INPUT_GOOGLE_PLACE_SEARCH;

        $page1_questions['server_key'] = "full_address";

        $page1_questions['is_required'] = YES;

        $page1_questions['value'] = $host ? $host->full_address : "";

        $page1_questions['latitude'] = $host ? $host->latitude : "";

        $page1_questions['longitude'] = $host ? $host->longitude : "";

        $page1_questions['full_address'] = $host ? $host->full_address : "";

        $page1_questions['street_details'] = $host ? $host->street_details : "";

        $page1_questions['city'] = $host ? $host->city : "";

        $page1_questions['state'] = $host ? $host->state : "";

        $page1_questions['country'] = $host ? $host->country : "";

        $page1_questions['zipcode'] = $host ? $host->zipcode : "";

        array_push($page1_questions_data, $page1_questions);

        $page1->data = $page1_questions_data;

        array_push($data, $page1);

        /*** @ @ @ Don't remove. We may use in upcoming versions @ @ @ ****/

        // $page2 = new \stdClass;

        // $page2->title = tr('api_map_location_adjust_title');

        // $page2->description = tr('api_map_location_adjust_description');

        // $page2_questions = $page2_questions_data = [];

        // $page2_questions['type'] = MAP_VIEW;

        // $page2_questions['question'] = $page2_questions['placeholder'] = $page2_questions['server_key'] = $page2_questions['answer'] = "";

        // array_push($page2_questions_data, $page2_questions);

        // $page2->data = $page2_questions_data;

        // array_push($data, $page2);

        return $data;
    
    }

    /**
     *
     * @method host_step_3()
     *
     * @uses used to get the amenties list
     *
     * @created Vidhya R
     *
     * @updated Vidhya R
     *
     * @param 
     *
     * @return
     */

    public static function host_step_3($request) {

        $page1 = $data = [];


        /* * * * * * Section 1 start * * * * * * * */

        $page1 = new \stdClass;

        $page1->title = $page1->description = "";

        $common_questions = CommonQuestion::where('question_static_type', 'amenties')->where('status', APPROVED)->get();

        $page1_questions_data = [];

        foreach ($common_questions as $key => $common_question_details) {

            $page1_questions = [];

            $page1_questions['question'] = $common_question_details->provider_question;

            $page1_questions['description'] = "";

            $page1_questions['type'] = $common_question_details->question_input_type;

            $page1_questions['server_key'] = "amenties_".$common_question_details->id;

            $page1_questions['is_required'] = NO;

            $common_question_answers = [];

            // Based on the question type load the answers

            $text_inputs = [INPUT, INPUT_NUMBER, INPUT_TEXT, INPUT_TEXTAREA];

            if(in_array($common_question_details->question_input_type, $text_inputs)) {

                if($request->host_id) {

                    $host_question_details = HostQuestionAnswer::where('host_id', $request->host_id)->where('common_question_id', $common_question_details->id)->first();

                    $common_question_answers = $host_question_details->answers ?? "";

                }

            } else {

                // Checkbox, Radio and select

                $common_question_answers = CommonQuestionAnswer::where('common_question_id', $common_question_details->id)->where('status', APPROVED)->select('id', 'common_answer as value')->get();

                foreach ($common_question_answers as $key => $common_question_answer_details) {

                    $common_question_answer_details->key = $common_question_answer_details->id;

                    // * * *  Check the answer is already marked start * * * //

                    $is_checked = NO;

                    if($request->host_id) {

                        $host_question_details = HostQuestionAnswer::where('host_id', $request->host_id)->where('common_question_id', $common_question_details->id)->first();

                        $host_answers_ids = $host_question_details->common_question_answer_id ?? "";

                        $host_answers_ids = $host_answers_ids ? explode(',', $host_answers_ids): [];

                        $is_checked = in_array($common_question_answer_details->id, $host_answers_ids) ? YES : NO;

                    }

                    $common_question_answer_details->is_checked = $is_checked;

                    // * * *  Check the answer is already marked end * * * //

                    $common_question_answer_details->is_required = YES;

                    $common_question_answer_details->picture = $common_question_answer_details->description = "";
                    
                    unset($common_question_answer_details->id);

                }

            }
        
            unset($common_question_details->id);

            $page1_questions['answer'] = $common_question_answers;

            array_push($page1_questions_data, $page1_questions);

            $page1->data = $page1_questions_data;

        }

        // End

        array_push($data, $page1);
        return $data;
    
    }

    /**
     *
     * @method host_step_4()
     *
     * @uses title, description and photos
     *
     * @created Vidhya R
     *
     * @updated Vidhya R
     *
     * @param 
     *
     * @return
     */

    public static function host_step_4($request) {

        $host = $host_details = [];

        if($request->host_id) {

            $host = Host::where('provider_id', $request->id)->where('id', $request->host_id)->first();

            $host_details = HostDetails::where('host_id', $request->host_id)->where('provider_id', $request->id)->first();
        }

        $data = [];

        /* * * * * * Section 1 start * * * * * * * */

        $page1 = new \stdClass;

        $page1->title = tr('api_host_details_page_title');

        $page1->description = tr('api_host_details_page_description');

        $page1_questions_data = $page1_questions = [];

        // Title

        $page1_questions = [];

        $page1_questions['question'] = tr('api_host_name');

        $page1_questions['description'] = tr('api_host_name_description');

        $page1_questions['placeholder'] = tr('api_host_name_placeholder');

        $page1_questions['type'] = INPUT;

        $page1_questions['input_type'] = INPUT_TEXT;

        $page1_questions['server_key'] = "host_name";

        $page1_questions['is_required'] = YES;

        $page1_questions['answer'] = $host ? $host->host_name : "";

        array_push($page1_questions_data, $page1_questions);

        // Title

        $page1_questions = [];

        $page1_questions['question'] = tr('api_host_description');

        $page1_questions['description'] = tr('api_host_description_description');

        $page1_questions['placeholder'] = tr('api_host_description_placeholder');

        $page1_questions['type'] = INPUT;

        $page1_questions['input_type'] = INPUT_TEXTAREA;

        $page1_questions['server_key'] = "description";

        $page1_questions['is_required'] = YES;

        $page1_questions['answer'] = $host ? $host->description : "";;

        array_push($page1_questions_data, $page1_questions);

        // Page 1 - section end

        $page1->data = $page1_questions_data;

        array_push($data, $page1);

        // Photo

        $page2 = new \stdClass;

        $page2->title = "";

        $page2->description = "";

        $page2_questions_data = $page2_questions = [];

        $page2_questions['question'] = tr('api_host_photo_title');

        $page2_questions['description'] = tr('api_host_photo_description');
        
        $page2_questions['type'] = UPLOAD;

        $page2_questions['input_type'] = UPLOAD_MULTIPLE;

        $page2_questions['server_key'] = "picture";

        $page2_questions['is_required'] = YES;

        $page2_questions['answer'] = HostGallery::where('host_id', $request->host_id)->CommonResponse()->get();

        array_push($page2_questions_data, $page2_questions);

        // Page 2 - section end

        $page2->data = $page2_questions_data;

        array_push($data, $page2);

        // Page 2 data end 

        return $data;

    }

    /**
     *
     * @method host_step_5()
     *
     * @uses used to get the amenties list
     *
     * @created Vidhya R
     *
     * @updated Vidhya R
     *
     * @param 
     *
     * @return
     */

    public static function host_step_5($request) {

        $host = $host_details = [];

        if($request->host_id) {

            $host = Host::where('provider_id', $request->id)->where('id', $request->host_id)->first();

            $host_details = HostDetails::where('host_id', $request->host_id)->where('provider_id', $request->id)->first();
        }


        $data = [];

        /* * * * * * Section 1 start * * * * * * * */

        $page1 = new \stdClass;

        $page1->title = tr('api_booking_page1_title');

        $page1->description = tr('api_booking_page1_description');

        $page1_questions_data = [];

        // Checkin

        $page1_questions = [];

        $page1_questions['question'] = tr('api_checkin_title');

        $page1_questions['description'] = tr('api_checkin_description');

        $page1_questions['placeholder'] = tr('api_checkin_placeholder');

        $page1_questions['type'] = SPINNER;

        $page1_questions['is_required'] = YES;

        $page1_questions['server_key'] = "checkin";

        $get_times = Helper::get_times();

        $check_in_out_data = [];

        foreach ($get_times as $key => $get_time) {

            $check_in_out['key'] = $key;

            $check_in_out['value'] = $get_time;

            $check_in_out['is_checked'] = NO;

            if($host) {
                
                $check_in_out['is_checked'] = $host->checkin == $key ? YES : NO;

            }

            array_push($check_in_out_data, $check_in_out);

        }

        $page1_questions['answer'] = $check_in_out_data;

        array_push($page1_questions_data, $page1_questions);


        // Checkout

        $page1_questions = [];

        $page1_questions['question'] = tr('api_checkout_title');

        $page1_questions['description'] = tr('api_checkout_description');

        $page1_questions['placeholder'] = tr('api_checkout_placeholder');

        $page1_questions['type'] = SPINNER;

        $page1_questions['is_required'] = YES;

        $page1_questions['server_key'] = "checkout";

        $checkput_get_times = Helper::get_times();

        $check_in_out_data = [];

        foreach ($checkput_get_times as $key => $get_time) {

            $check_in_out['key'] = $key;

            $check_in_out['value'] = $get_time;

            $check_in_out['is_checked'] = NO;

            if($host) {
                
                $check_in_out['is_checked'] = $host->checkout == $key ? YES : NO;

            }

            array_push($check_in_out_data, $check_in_out);

        }

        $page1_questions['answer'] = $check_in_out_data;

        array_push($page1_questions_data, $page1_questions);

        // Page 1 - section end

        $page1->data = $page1_questions_data;

        array_push($data, $page1);


        // Trip Length

        $page2 = new \stdClass;

        $page2->title = "Trip Length";

        $page2->description = "Configure, how long can guests stay.";

        // Min stay

        $page2_questions_data = $page2_questions = [];

        $page2_questions['question'] = tr('api_min_stay_title');

        $page2_questions['description'] = tr('api_min_stay_description');

        $page2_questions['input_type'] = INPUT_NUMBER;

        $page2_questions['server_key'] = "min_days";

        $page2_questions['type'] = INPUT;

        $page2_questions['is_required'] = YES;

        $page2_questions['answer'] = $host ? $host->min_days: 0;

        array_push($page2_questions_data, $page2_questions);

        // Max stay 

        $page2_questions = [];

        $page2_questions['question'] = tr('api_max_stay_title');

        $page2_questions['description'] = tr('api_max_stay_description');
        
        $page2_questions['server_key'] = "max_days";

        $page2_questions['type'] = INPUT;

        $page2_questions['input_type'] = INPUT_NUMBER;

        $page2_questions['is_required'] = YES;

        $page2_questions['answer'] = $host ? $host->max_days: 0;

        array_push($page2_questions_data, $page2_questions);

        // Page 2 - section end

        $page2->data = $page2_questions_data;

        array_push($data, $page2);

        // Page 2 data end 

        return $data;

    }
    
    /**
     *
     * @method host_step_6()
     *
     * @uses used to get the amenties list
     *
     * @created Vidhya R
     *
     * @updated Vidhya R
     *
     * @param 
     *
     * @return
     */

    public static function host_step_6($request) {
        
        $host = $host_details = [];

        if($request->host_id) {

            $host = Host::where('provider_id', $request->id)->where('id', $request->host_id)->first();

            $host_details = HostDetails::where('host_id', $request->host_id)->where('provider_id', $request->id)->first();
        }

        $data = [];

        /* * * * * * Section 1 start * * * * * * * */

        $page1 = new \stdClass;

        $page1->title = tr('api_pricing_page_title');

        $page1->description = tr('api_pricing_page_description', $host_details->min_guests ?? 1);

        $page1_questions_data = $page1_questions = [];

        // Base Price

        /*$page1_questions = [];

        $page1_questions['question'] = tr('api_base_price_title');

        $page1_questions['description'] = tr('api_base_price_description');

        $page1_questions['placeholder'] = tr('api_base_price_placeholder');

        $page1_questions['type'] = INPUT;

        $page1_questions['input_type'] = INPUT_NUMBER;

        $page1_questions['server_key'] = "base_price";

        $page1_questions['answer'] = $host ? $host->base_price : 0;

        array_push($page1_questions_data, $page1_questions);*/

        // Per day

        $page1_questions = [];

        $page1_questions['question'] = tr('api_per_day_title');

        $page1_questions['description'] = tr('api_per_day_description');

        $page1_questions['placeholder'] = tr('api_per_day_placeholder');

        $page1_questions['type'] = INPUT;

        $page1_questions['input_type'] = INPUT_NUMBER;

        $page1_questions['server_key'] = "per_day";

        $page1_questions['answer'] = $host ? $host->per_day : 0;

        array_push($page1_questions_data, $page1_questions);

        // Additional guests price

        $page1_questions = [];

        $page1_questions['question'] = tr('api_per_guest_price_title');

        $page1_questions['description'] = tr('api_per_guest_price_description');

        $page1_questions['placeholder'] = tr('api_per_guest_price_placeholder');

        $page1_questions['type'] = INPUT;

        $page1_questions['input_type'] = INPUT_NUMBER;

        $page1_questions['server_key'] = "per_guest_price";

        $page1_questions['answer'] = $host ? $host->per_guest_price : 0;

        array_push($page1_questions_data, $page1_questions);

        // Per Week

        /* $page1_questions = [];

        $page1_questions['question'] = tr('api_per_week_title');

        $page1_questions['description'] = tr('api_per_week_description');

        $page1_questions['placeholder'] = tr('api_per_week_placeholder');

        $page1_questions['type'] = INPUT;

        $page1_questions['input_type'] = INPUT_NUMBER;

        $page1_questions['server_key'] = "per_week";

        $page1_questions['answer'] = $host ? $host->per_week : 0;

        array_push($page1_questions_data, $page1_questions);*/

        // Per Month

        /*$page1_questions = [];

        $page1_questions['question'] = tr('api_per_month_title');

        $page1_questions['description'] = tr('api_per_month_description');

        $page1_questions['placeholder'] = tr('api_per_month_placeholder');

        $page1_questions['type'] = INPUT;

        $page1_questions['input_type'] = INPUT_NUMBER;

        $page1_questions['server_key'] = "per_month";

        $page1_questions['answer'] = $host ? $host->per_month : 0;

        array_push($page1_questions_data, $page1_questions);*/

        // Cleaning Fee

        $page1_questions = [];

        $page1_questions['question'] = tr('api_cleaning_fee_title');

        $page1_questions['description'] = tr('api_cleaning_fee_description');

        $page1_questions['placeholder'] = tr('api_cleaning_fee_placeholder');

        $page1_questions['type'] = INPUT;

        $page1_questions['input_type'] = INPUT_NUMBER;

        $page1_questions['server_key'] = "cleaning_fee";

        $page1_questions['answer'] = $host ? $host->cleaning_fee : 0;

        array_push($page1_questions_data, $page1_questions);

        // Page 1 - section end

        $page1->data = $page1_questions_data;

        // Page 2 @todo rules and policies

        array_push($data, $page1);

        // Page 1 data end 
        return $data;

    }

    /**
     *
     * @method host_step_7()
     *
     * @uses used to availability details
     * 
     * @created Vidhya R
     *
     * @updated Vidhya R
     *
     * @param 
     *
     * @return
     */

    public static function host_step_7($request) {

        $host = $host_details = [];

        if($request->host_id) {

            $host = Host::where('provider_id', $request->id)->where('id', $request->host_id)->first();

            $host_details = HostDetails::where('host_id', $request->host_id)->where('provider_id', $request->id)->first();
        }

        $data = [];

        /* * * * * * Section 1 start * * * * * * * */

        $page1 = new \stdClass;

        $page1->title = tr('HOST_STEP_7');

        $page1->description = "";

        $page1_questions_data = $page1_questions = [];


        $page1_questions = [];

        $page1_questions['question'] = tr('HOST_STEP_7');

        $page1_questions['description'] = $page1_questions['placeholder'] = "";

        $page1_questions['type'] = AVAILABILITY_CALENDAR;

        $page1_questions['server_key'] = "";

        $page1_questions['is_required'] = YES;

        $page1_questions['answer'] = [];

        array_push($page1_questions_data, $page1_questions);

        // Page 1 - section end

        $page1->data = $page1_questions_data;

        array_push($data, $page1);

        // Page 1 data end 

        return $data;

    }

    /**
     *
     * @method host_step_8()
     *
     * @uses used to availability details
     * 
     * @created Vidhya R
     *
     * @updated Vidhya R
     *
     * @param 
     *
     * @return
     */

    public static function host_step_8($request) {

        $host = $host_details = [];

        if($request->host_id) {

            $host = Host::where('provider_id', $request->id)->where('id', $request->host_id)->first();

            $host_details = HostDetails::where('host_id', $request->host_id)->where('provider_id', $request->id)->first();
        }

        $data = [];

        /* * * * * * Section 1 start * * * * * * * */

        $page_data = new \stdClass;

        $page_data->title = tr('HOST_STEP_8_title');

        $page_data->description = tr('HOST_STEP_8_description');

        $page_data->type = "REVIEW";

        $page_data->question = "REVIEW LISTING";

        // Page question start

        $page1_questions = $page1_questions_data = [];

        $page1_questions['title'] = tr('HOST_STEP_8_title');

        $page1_questions['description'] = tr('HOST_STEP_8_description');

        $page1_questions['question'] = "Review Listing";

        // Host Title start

        $question1 = $question1_data = $answer_data =[];

        $question1 = new \stdClass;

        $question1->title = tr('title');

        $question1->type = REVIEW;

        // Title start

        $title_answer_data['title'] = tr('title');

        $title_answer_data['description'] = [];

        if($host) {

            $title_answer_data['description'] = [$host ? $host->host_name : ""];
        }

        array_push($answer_data, $title_answer_data);

        // Title end 

        // availability start

        $avail_answer_data['title'] = tr('basic_details');

        $avail_desc_data = [];

        if($host) {

            $avail_desc_data = [tr('location').": ".$host->full_address ?: ""];

            $avail_desc_data[] = tr('host_type').": ".$host->host_type ?: "";

            $avail_desc_data[] = tr('min_guests').": ".$host_details->min_guests ?: 0;

            $avail_desc_data[] = tr('max_guests').": ".$host_details->max_guests ?: 0;

            $avail_desc_data[] = tr('total_guests').": ".$host_details->total_guests ?: 0;

        }

      

        $avail_answer_data['description'] = $avail_desc_data;

        array_push($answer_data, $avail_answer_data);

        // availability end

        // Billing start
        
        $billing_data['title'] = tr('billing_details');

        $pricing_data = [];

        if($host) {

            $pricing_data = [tr('per_day').": ".formatted_amount($host->per_day) ?: "$0.00"];

            $pricing_data[] = tr('per_guest_price').": ".formatted_amount($host->per_guest_price) ?: "$0.00";
            
            $pricing_data[] = tr('cleaning_fee').": ".formatted_amount($host->cleaning_fee) ?: "$0.00";
        }

        $billing_data['description'] = $pricing_data;

        array_push($answer_data, $billing_data);

        // Billing end

        $question1->answer = $answer_data;

        array_push($question1_data, $question1);

        // UPDATE THE ANSWER

        $page_data->data = $question1_data;

        $data = [];

        array_push($data, $page_data);

        return $data;

    }

    /**
     *
     * @method provider_hosts_response()
     *
     * @uses used to get the common list details for hosts
     *
     * @created Vidhya R
     *
     * @updated Vidhya R
     *
     * @param 
     *
     * @return
     */

    public static function provider_hosts_response($host_ids) {

        $hosts = Host::whereIn('hosts.id' , $host_ids)
                        ->select('hosts.id as host_id', 'hosts.host_name', 'hosts.picture as host_picture', 'hosts.host_type', 'hosts.city as host_location', 'hosts.created_at', 'hosts.updated_at')
                        ->orderBy('hosts.updated_at' , 'desc')
                        ->get();
        return $hosts;

    } 

    /**
     *
     * @method host_gallery_upload()
     *
     * @uses used to get the common list details for hosts
     *
     * @created Vidhya R
     *
     * @updated Vidhya R
     *
     * @param 
     *
     * @return
     */

    public static function host_gallery_upload($files, $host_id, $status = YES, $set_default_picture = NO) {

        $allowedfileExtension=['jpeg','jpg','png'];

        $host = Host::find($host_id);

        $is_host_image = $host ? ($host->picture ? YES : NO): NO;

        $data = [];

        // Single file upload

        if(!is_array($files)) {
            
            $file = $files;

            $host_gallery_details = new HostGallery;

            $host_gallery_details->host_id = $host_id;

            $host_gallery_details->picture = Helper::upload_file($file, FILE_PATH_HOST);

            $host_gallery_details->status = $status;

            $host_gallery_details->save();

            if($is_host_image == NO && $host) {

                $host->picture = $host_gallery_details->picture;

                $host->save();
            }

            if($set_default_picture == YES) {

                $host->picture = $host_gallery_details->picture;

                $host->save();

            }

            $gallery_data = [];

            $gallery_data['host_gallery_id'] = $host_gallery_details->id;

            $gallery_data['file'] = $gallery_data['picture'] = $host_gallery_details->picture;

            array_push($data, $gallery_data);

            return $data;
       
        }

        // Multiple files upload

        foreach($files as $file) {

            $filename = $file->getClientOriginalName();

            $extension = $file->getClientOriginalExtension();

            $check_picture = in_array($extension, $allowedfileExtension);
            
            if($check_picture) {

                $host_gallery_details = new HostGallery;

                $host_gallery_details->host_id = $host_id;

                $host_gallery_details->picture = Helper::upload_file($file, FILE_PATH_HOST);

                $host_gallery_details->status = $status;

                $host_gallery_details->save();

                if($is_host_image == NO && $host) {

                    $host->picture = $host_gallery_details->picture;

                    $host->save();
               
                }

                $gallery_data = [];

                $gallery_data['host_gallery_id'] = $host_gallery_details->id;

                $gallery_data['file'] = $host_gallery_details->picture;

                array_push($data, $gallery_data);

           }
        
        }

        return $data;
    
    }

    /**
     *
     * @method hosts_save()
     *
     * @uses used to get the common list details for hosts
     *
     * @created Vidhya R
     *
     * @updated Vidhya R
     *
     * @param 
     *
     * @return
     */

    public static function hosts_save($request, $device_type = DEVICE_ANDROID) {

        try {
            
            $host_id = $request->host_id;

            if($host_id) {

                $host = Host::find($host_id);

                if(!$host) {
                    throw new Exception( Helper::error_message(200), 200);
                }

                $host_details = HostDetails::where('provider_id', $request->id)->where('host_id', $request->host_id)->first();

            } else {

                $host = new Host;

                $host->provider_id = $request->id;

                $host->save();

                $host_details = new HostDetails;
            
            }


            $host->category_id = $request->category_id ?: ($host->category_id ?: 0);

            $host->sub_category_id = $request->sub_category_id ?: ($host->sub_category_id ?: 0);

            $host->host_type = $request->host_type ?: ($host->host_type ?: "");

            $host->host_name = $request->host_name ?: ($host->host_name ?: "");

            $host->description = $request->description ?: $host->description;

            $host->save();

            /***** Host pictures upload ****/

            if($request->hasfile('picture')) {

                self::host_gallery_upload($request->file('picture'), $host->id);
            
            }

            /***** Host pictures upload ****/

            $host_details = $host_details ?: new HostDetails;

            $host_details->host_id = $host ? $host->id : 0;

            $host_details->provider_id = $request->id;

            // $host_details->total_guests = $request->total_guests ?: ($host_details->total_guests ?: 0);
            
            $host_details->min_guests = $request->min_guests ?: ($host_details->min_guests ?: 0);
            
            $host_details->max_guests = $host_details->total_guests = $request->max_guests ?: ($host_details->max_guests ?: 0);
            
            $host_details->total_bedrooms = $request->total_bedrooms ?: ($host_details->total_bedrooms ?: 0);

            $host_details->total_beds = $request->total_beds ?: ($host_details->total_beds ?: 0);

            $host_details->total_bathrooms = $request->total_bathrooms ?: ($host_details->total_bathrooms ?: 0);

            $host_details->bathroom_type = $request->bathroom_type ?: ($host_details->bathroom_type ?: 0);
            
            $host_details->save();

            // Step2

            $host->street_details = $request->street_details ?: ($host->street_details ?: "");

            $host->country = $request->country ?: $host->country;

            $host->city = $request->city ?: ($host->city ?: "");

            $host->state = $request->state ?: ($host->state ?: "");

            $host->latitude = $request->latitude ?: ($host->latitude ?: 0.00);

            $host->longitude = $request->longitude ?: ($host->longitude ?: 0.00);

            $host->full_address = $request->full_address ?: ($host->full_address ?: "");

            $host->zipcode = $request->zipcode ?: ($host->zipcode ?: "");

            $host->service_location_id = $request->service_location_id ?: ($host->service_location_id ?: 0);

            $host->save();

            // Step 3 - Update Amenties details - Sample 1: amenties_123 = 1,2,3 Sample 2: amenties_100 = "Hello World"

            if($request->step == HOST_STEP_3 || $device_type == DEVICE_WEB) {

                $amenties = array_search_partial($request->all(), 'amenties_');

                if($amenties) {

                    foreach ((array) $amenties as $amenties_key => $amenties_value) {

                        if(!IsNullOrEmptyString($amenties_value)) {

                            Log::info("amenties_value".$amenties_value);

                            Log::info("amenties_key".$amenties_key);

                            $common_questions = CommonQuestion::find($amenties_key);

                            // Check the question record exists

                            if($common_questions) {

                                // Check the already exists

                                $check_host_amenties = $host_amenties = HostQuestionAnswer::where('host_id', $host->id)->where('common_question_id', $amenties_key)->first();

                                if(!$check_host_amenties) {

                                    $host_amenties = new HostQuestionAnswer;

                                    $host_amenties->provider_id = $request->id;

                                    $host_amenties->host_id = $host->id;

                                    $host_amenties->common_question_id = $amenties_key;

                                }

                                // Check the question type and update the answers based on that

                                $text_inputs = [INPUT, INPUT_NUMBER, INPUT_TEXT, INPUT_TEXTAREA];

                                if(in_array($common_questions->question_input_type, $text_inputs)) {

                                    $host_amenties->common_question_answer_id = "";

                                    $host_amenties->answers = $amenties_value ?: "";

                                    $host_amenties->save();

                                } else {

                                    // Checkbox, Select and radio buttons

                                    $common_question_answer_ids = $amenties_value ? explode(',', $amenties_value) : [];

                                    // Check the value exists

                                    if($common_question_answer_ids) {

                                        $checkbox_radio_select_answers = $answer_ids = [];

                                        foreach ($common_question_answer_ids as $key => $answer_id) {

                                            if($answer_id) {
                                                
                                                $common_question_answer = CommonQuestionAnswer::find($answer_id);

                                                if($common_question_answer) {

                                                    $checkbox_radio_select_answers[] = $common_question_answer->common_answer;

                                                    $answer_ids[] = $answer_id;
 
                                                }
                                                
                                            }
                                        }

                                        // Check answers are not empty

                                        if(!empty($checkbox_radio_select_answers)) {

                                            $checkbox_radio_select_answers = implode($checkbox_radio_select_answers, ',');
                                       
                                            $host_amenties->answers = $checkbox_radio_select_answers ?? "";

                                            $host_amenties->save();
                                        }

                                        // Check answer ids are not empty

                                        if(!empty($answer_ids)) {

                                            $host_amenties->common_question_answer_id = implode(",", $answer_ids);

                                            $host_amenties->save();

                                        }

                                    }

                                }

                            } else {

                                Log::info("common_questions and ids are empty");
                            }
                        
                        }

                    }
                }
           
            }
            
            // Step 5 & 6

            $host->checkin = $request->checkin ?: ($host->checkin ?: "");

            $host->checkout = $request->checkout ?: ($host->checkout ?: "");

            $host->min_days = $request->min_days ?: ($host->min_days ?: 0);

            $host->max_days = $request->max_days ?: ($host->max_days ?: 0);

            $host->base_price = $request->base_price ?: ($host->base_price ?: 0);

            $host->per_day = $request->per_day ?: ($host->per_day ?: 0);

            $host->per_guest_price = $request->per_guest_price ?: ($host->per_guest_price ?: 0);

            $host->per_week = $request->per_week ?: ($host->per_week ?: 0);

            $host->per_month = $request->per_month ?: ($host->per_month ?: 0);

            $host->cleaning_fee = $request->cleaning_fee ?: ($host->cleaning_fee ?: 0);

            $host->save();

            $host = Host::find($host->id);

            $response_array = ['success' => true, 'host_details' => $host_details, 'host' => $host];
            
            return $response_array;

        } catch(Exception $e) {

            $response_array = ['success' => false, 'error' => $e->getMessage(), 'error_code' => $e->getCode()];

            return $response_array;
        }
    
    }

    public static function see_all_section($request) {

        $hosts = []; $title = $description = "";

        switch ($request->url_type) {

            case URL_TYPE_RECENT_UPLOADED:
                $hosts = HostHelper::recently_uploaded_hosts($request);
                $title = tr('URL_TYPE_RECENT_UPLOADED');
                $description = "";
                break;

            case URL_TYPE_TOP_RATED:
                $hosts = HostHelper::top_rated_hosts($request);
                $title = tr('URL_TYPE_TOP_RATED');
                $description = "";
                break;

            case URL_TYPE_SUGGESTIONS:
                $hosts = HostHelper::suggestions($request);
                $title = tr('URL_TYPE_SUGGESTIONS');
                $description = "";
                break;

            case URL_TYPE_CATEGORY:
                $request->request->add(['category_id' => $request->url_page_id]);
                $hosts = HostHelper::category_based_hosts($request);
                $title = tr('URL_TYPE_CATEGORY');
                $description = "";
                break;

            case URL_TYPE_SUB_CATEGORY:
                $request->request->add(['sub_category_id' => $request->url_page_id]);
                $hosts = HostHelper::sub_category_based_hosts($request);
                $title = tr('URL_TYPE_SUB_CATEGORY');
                $description = "";
                break;

            default:
                $hosts = HostHelper::suggestions($request);
                $title = tr('URL_TYPE_SUGGESTIONS');
                $description = "";
                break;
        
        }

        $data['title'] = $title;

        $data['description'] = $description;

        $data['data'] = $hosts;

        return $data;
    }

    /**
     * @method bookings_payment_by_stripe
     *
     * @uses stripe payment for booking
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param
     *
     * @return
     */
    
    public function bookings_payment_by_stripe($request, $booking_details) {

        try {

            DB::beginTransaction();

            // Check provider card details

            $card_details = UserCard::where('user_id', $request->id)->where('is_default', YES)->first();

            if (!$card_details) {

                throw new Exception(Helper::error_message(111), 111);
            }

            $customer_id = $card_details->customer_id;

            // Check stripe configuration
        
            $stripe_secret_key = Setting::get('stripe_secret_key');

            if(!$stripe_secret_key) {

                throw new Exception(Helper::error_message(107), 107);

            } 

            \Stripe\Stripe::setApiKey($stripe_secret_key);

            $total = $booking_details->total;

            $currency_code = Setting::get('currency_code', 'AUD') ?: "AUD";

            $charge_array = [
                                "amount" => $total * 100,
                                "currency" => $currency_code,
                                "customer" => $customer_id,
                            ];

            $stripe_payment_response =  \Stripe\Charge::create($charge_array);

            $payment_id = $stripe_payment_response->id;

            $paid_amount = $stripe_payment_response->amount/100;

            $paid_status = $stripe_payment_response->paid;

            DB::commit();

            $booking_payment = new BookingPayment;

            $booking_payment->booking_id = $booking_details->id;

            $booking_payment->user_id = $booking_details->user_id;

            $booking_payment->provider_id = $booking_details->provider_id;

            $booking_payment->host_id = $booking_details->host_id;

            $booking_payment->payment_id = $payment_id;

            $booking_payment->payment_mode = CARD;

            $booking_payment->currency = Setting::get('currency', '$');

            $booking_payment->total_time = $booking_details->total_days;

            $booking_payment->time_price = $booking_details->total;

            $booking_payment->sub_total = $booking_payment->actual_total = $booking_payment->total = $booking_details->total;

            $booking_payment->paid_amount = $paid_amount;

            $booking_payment->paid_date = date('Y-m-d H:i:s');

            $booking_payment->admin_amount = 0.00;

            $booking_payment->provider_amount = 0.00;

            $booking_payment->status = PAID;

            $booking_payment->save();

            // Commission spilit for bookings

            $commission_details = booking_commission_spilit($booking_details->total);

            $booking_payment->admin_amount = $commission_details->admin_amount ?: 0.00;

            $booking_payment->provider_amount = $commission_details->provider_amount ?: 0.00;

            $booking_payment->save();


        } catch(Stripe_CardError | Stripe_InvalidRequestError | Stripe_AuthenticationError | Stripe_ApiConnectionError | Stripe_Error $e) {         

            DB::commit();

            return $this->sendError($e->getMessage(), $e->getCode());

        } catch(Exception $e) {

            // Something else happened, completely unrelated to Stripe

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());

        }
    }

    /**
     *
     * @method spaces_save()
     *
     * @uses used to get the common list details for hosts
     *
     * @created Vidhya R
     *
     * @updated Vidhya R
     *
     * @param 
     *
     * @return
     */

    public static function spaces_save($request) {

        try {
            
            $host_id = $request->host_id;

            if($host_id) {

                $host = Host::find($host_id);

                if(!$host) {
                    throw new Exception( Helper::error_message(200), 200);
                }

                $host_details = HostDetails::where('provider_id', $request->id)->where('host_id', $request->host_id)->first();

            } else {

                $host = new Host;

                $host->provider_id = $request->id;

                $host->save();

                $host_details = new HostDetails;
            
            }

            $host->host_type = $request->host_type ?: ($host->host_type ?: "");

            $host->host_name = $request->host_name ?: ($host->host_name ?: "");

            $host->description = $request->description ?: $host->description;


            $host->access_note = $request->access_note ?: $host->access_note;

            $host->access_method = $request->access_method ?: $host->access_method;
            
            $host->security_code = $request->security_code ?: $host->security_code;


            $host->host_owner_type = $request->host_owner_type ?: $host->host_owner_type;

            $host->total_spaces = $request->total_spaces ?: ($host->total_spaces ?: 1);

            $host->width_of_space = $request->width_of_space ?: $host->width_of_space;

            $host->height_of_space = $request->height_of_space ?: $host->height_of_space;

            $host->amenities = $request->amenities ?: $host->amenities;

            $host->save();

            /***** Host pictures upload ****/

            if($request->hasfile('picture')) {

                self::host_gallery_upload($request->file('picture'), $host->id, YES, $set_default_picture = YES);
            
            }

            /***** Host pictures upload ****/

            $host_details = $host_details ?: new HostDetails;

            $host_details->host_id = $host ? $host->id : 0;

            $host_details->provider_id = $request->id;

            $host_details->save();

            // Step2

            $host->street_details = $request->street_details ?: ($host->street_details ?: "");

            $host->country = $request->country ?: $host->country;

            $host->city = $request->city ?: ($host->city ?: "");

            $host->state = $request->state ?: ($host->state ?: "");

            $host->latitude = $request->latitude ?: ($host->latitude ?: 0.00);

            $host->longitude = $request->longitude ?: ($host->longitude ?: 0.00);

            $host->full_address = $request->full_address ?: ($host->full_address ?: "");

            $host->zipcode = $request->zipcode ?: ($host->zipcode ?: "");

            $host->service_location_id = $request->service_location_id ?: ($host->service_location_id ?: 0);

            $host->save();

            // Step 3 - Update Amenties details

            if($request->step == HOST_STEP_3) {

                $amenties = array_search_partial($request->all(), 'amenties_');

                foreach ((array) $amenties as $amenties_key => $amenties_value) {

                    // Check the already exists

                    $check_host_amenties = HostQuestionAnswer::where('host_id', $host->id)->where('common_question_id', $amenties_key)->first();

                    if(!$check_host_amenties) {

                        $host_amenties = new HostQuestionAnswer;

                        $host_amenties->provider_id = $request->id;

                        $host_amenties->host_id = $host->id;

                        $host_amenties->common_question_id = $amenties_key;

                        $host_amenties->common_question_answer_id = $amenties_value;

                        $host_amenties->save();

                    } else {

                        $check_host_amenties->common_question_answer_id = $amenties_value;

                        $check_host_amenties->save();
                    }

                }
            
            }

            // Step 5 & 6

            $host->checkin = $request->checkin ?: ($host->checkin ?: "");

            $host->checkout = $request->checkout ?: ($host->checkout ?: "");

            $host->min_days = $request->min_days ?: ($host->min_days ?: 0);

            $host->max_days = $request->max_days ?: ($host->max_days ?: 0);

            $host->base_price = $request->base_price ?: ($host->base_price ?: 0);


            $host->per_hour = $request->per_hour ?: ($host->per_hour ?: 0);

            $host->per_day = $request->per_day ?: ($host->per_day ?: 0);

            $host->per_week = $request->per_week ?: ($host->per_week ?: 0);

            $host->per_month = $request->per_month ?: ($host->per_month ?: 0);

            $host->cleaning_fee = $request->cleaning_fee ?: ($host->cleaning_fee ?: 0);

            $host->save();

            $response_array = ['success' => true, 'host_details' => $host_details, 'host' => $host];

            return $response_array;

        } catch(Exception $e) {

            $response_array = ['success' => false, 'error' => $e->getMessage(), 'error_code' => $e->getCode()];

            return $response_array;
        }
    
    }

    /**
     * Get the amenities details based on Question static type - Amenities,None
     */
    public static function host_question_answers_save($details,$host_id,$type) {

        foreach ($details as $key => $value) {

            if($value) {
                // Load the HostQuestionAnswer based on the request datas
                $hosts_question_answer = new HostQuestionAnswer;
                $hosts_question_answer->host_id = $host_id;
                $hosts_question_answer->common_question_id = $key;
                
                if($type == INPUT) {
                    $hosts_question_answer->answers = $value;
                } else {
                    $hosts_question_answer->common_question_answer_id = $value;
                    $common_question_answer = CommonQuestionAnswer::findOrFail($value);
                    $hosts_question_answer->answers = $common_question_answer->common_answer;
                }
                
                $hosts_question_answer->save();
            }
        }
    } 

    /**
     *
     * @method get_amenities_list()
     *
     * @uses Get the amenities details based on Question static type - Amenities, None
     *
     * @created Bhawya
     *
     * @updated Vithya
     *
     * @param
     * 
     */
    
    public static function get_amenities_list($type, $host_id, $host) {

        $category_id = [$host->category_id, 0];

        $sub_category_id = [$host->sub_category_id, 0];
        
        // Based on given question type load the common questions 

        $base_query = CommonQuestion::where('status', APPROVED)
                            ->whereIn('category_id', $category_id)
                            ->whereIn('sub_category_id', $sub_category_id);

        $type = is_array($type) ? $type : [$type];

        $amenties = $base_query->whereIn('question_static_type', $type)->get();
        
        foreach ($amenties as $key => $amenities_details) {
           
            // Based on Question input type load the ameinites details
            $select_input = [CHECKBOX, RADIO, SELECT];

            if(in_array($amenities_details->question_input_type, $select_input)) {

                // Load the answers for based on questions ID
                $amenities_answers = CommonQuestionAnswer::where('common_question_id', $amenities_details->id)
                            ->where('status', APPROVED)
                            ->select('id', 'common_answer as value')
                            ->get();

                // Assign the Answers  - For the view
                $amenities_details->answers = $amenities_answers;
                
                // For Edit - check if the question is already answered for the selected host Id
                foreach ($amenities_answers as $key => $amenities_answer) {
                   
                    // check the common question answer is answered
                    $host_question_answer = HostQuestionAnswer::where('host_id', $host_id)->UserAmentiesResponse($type)->pluck('common_question_answer_id');

                    $ids = [];
                    foreach ($host_question_answer as $key => $value) {
                        $ids[] = $value;
                    }
                    
                    $implode = explode(',', implode(',', $ids));
                    $value = in_array($amenities_answer->id, $implode);
                    $amenities_answer->is_selected = $value ? YES : NO;
                }

            }
            
            // Text area,input 

            if($amenities_details->question_input_type == INPUT) {
                
                // check the common question answer is answered
                $host_question_answer = HostQuestionAnswer::where('host_id', $host_id)->UserAmentiesResponse($type)->get();
                
                foreach ($host_question_answer as $key => $value) {

                    if($amenities_details->id == $value->common_question_id) {
                        // Directly assign the answer values
                        $amenities_details->answer = $value->answers;
                    }   
                }
            
            }
                
        }

        return $amenties;

    }

    /**
     * @method host_availablity_list_update()
     *
     * @uses based on the provider data, add/remove the spaces
     *
     * @created Vithya R
     * 
     * @updated Vithya R
     *
     * @param datetime $from_date 
     *
     * @param datetime $to_date 
     *
     * @return boolean
     */
    
    public static function host_availablity_list_update($request, $host_details) {

        try {

            $from_date = date('Y-m-d H'.":00:00", strtotime($request->from_date));

            $to_date = date('Y-m-d H'.":00:00", strtotime($request->to_date));

            $period = new \DatePeriod(
                     new \DateTime($from_date),
                     new \DateInterval('PT1H'),
                     new \DateTime($to_date)
                );

            foreach ($period as $key => $value) {

                $current_date = $value->format('Y-m-d');

                $current_time = $value->format('H'.':00:00');

                // Check the host availability record

                $host_availablity = HostAvailability::where('date', $current_date)->where('time', $current_time)->where('host_id', $host_details->id)->first();

                if(!$host_availablity) {

                    $host_availablity = new HostAvailability;

                    $host_availablity->date = $current_date;

                    $host_availablity->time = $current_time;

                    $host_availablity->host_id = $host_details->id;

                    $host_availablity->total_spaces = $host_availablity->remaining_spaces = $host_details->total_spaces ?: 0;

                    $host_availablity->save();
                
                }

                $host_availablity->slot = get_time_slot($current_time);

                if($request->type == 1) {

                    $host_availablity->total_spaces += $request->spaces;
                    
                    $host_availablity->remaining_spaces += $request->spaces;

                } else {
                    
                    $host_availablity->total_spaces -= $request->spaces;

                    $host_availablity->remaining_spaces -= $request->spaces;

                    if($host_availablity->total_spaces < 0) {

                        $host_availablity->total_spaces = 0;
                    }

                    if($host_availablity->remaining_spaces < 0) {
                        
                        $host_availablity->remaining_spaces = 0;
                    }

                }

                $host_availablity->save();

            }

        } catch(Exception $e) {

            Log::info("host_availablity_update - error".print_r($e->getMessage(), true));

        }

    }
}