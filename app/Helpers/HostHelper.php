<?php 

namespace App\Helpers;

use Hash, Exception, Log, Setting, DB;

use App\Repositories\BookingRepository as BookingRepo;

use App\Repositories\HostRepository as HostRepo;

use App\Admin, App\User, App\Provider;

use App\Wishlist;

use App\Host;

use App\ServiceLocation;

use App\CommonQuestion, App\CommonQuestionAnswer;

use App\HostQuestionAnswer;

use App\HostAvailability;

use Illuminate\Support\Carbon;

// use Carbon\Carbon;

use Carbon\CarbonPeriod;

use App\SubCategory;

use App\Category;

class HostHelper {

    /** 
     * @method check_valid_dates()
     *
     * @created vithya R
     *
     * @updated vithya R
     *
     * @param integer $host_id 
     * 
     * @param integer $user_id 
     *
     * @return boolean
     */
    
    public static function check_valid_dates($dates) {

        $list_dates = $dates ? explode(',', $dates) : [];

        $list_dates = array_filter($list_dates,function($date){
            return strtotime($date) > strtotime('today');
        });

        return $list_dates; 

    }

    /** 
     * @method wishlist_status()
     *
     * @created vithya R
     *
     * @updated vithya R
     *
     * @param integer $host_id 
     * 
     * @param integer $user_id 
     *
     * @return boolean
     */
    
    public static function wishlist_status($host_id, $user_id) {

        $wishlist_details = Wishlist::where('user_id', $user_id)->where('host_id', $host_id)->first();

        return $wishlist_details ? YES: NO;

    }

    /**
     *
     * @method locations_data()
     *
     * @uses used to get the list of hosts based on the location
     *
     * @created Vidhya R
     *
     * @updated Vidhya R
     *
     * @param integer $user_id
     *
     * @param integer $skip
     *
     * @return list of hosts
     */

    public static function locations_data($request) {

        try {

            $base_query = ServiceLocation::CommonResponse()->orderby('service_locations.created_at' , 'desc');

            $take = Setting::get('admin_take_count', 12);

            $skip = $request->skip ?: 0;

            $service_locations = $base_query->skip($skip)->take($take)->get();

            foreach ($service_locations as $key => $service_location_details) {

                $service_location_details->api_page_type_id = $service_location_details->service_location_id;
            }

            return $service_locations;

        }  catch( Exception $e) {

            Log::info($e->getMessage());

            return [];

        }

    }

    /**
     *
     * @method location_based_hosts()
     *
     * @uses used to get the list of hosts based on the location
     *
     * @created Vidhya R
     *
     * @updated Vidhya R
     *
     * @param integer $user_id
     *
     * @param integer $skip
     *
     * @return list of hosts
     */

    public static function location_based_hosts($request) {

        try {

            $service_location_id = is_array($request->service_location_id) ? $request->service_location_id : [];

            $base_query = Host::whereIn('hosts.service_location_id', $service_location_id)
                            ->orderby('hosts.created_at' , 'desc');

            $take = Setting::get('admin_take_count', 12);

            $skip = $request->skip ?: 0;

            $host_ids = $base_query->skip($skip)->take($take)->pluck('hosts.id');

            $host_ids = $host_ids ? $host_ids->toArray() : [];

            $hosts = HostRepo::host_list_response($host_ids, $request->id);

            return $hosts;

        }  catch( Exception $e) {

            Log::info($e->getMessage());

            return [];

        }

    }

    /**
     *
     * @method recently_uploaded_hosts()
     *
     * @uses used to get the list of hosts based on the recently uploaded
     *
     * @created Vidhya R
     *
     * @updated Vidhya R
     *
     * @param integer $user_id
     *
     * @param integer $sub_profile_id
     *
     * @param integer $skip
     *
     * @return list of hosts
     */

    public static function recently_uploaded_hosts($request) {

        try {

            $base_query = Host::VerifedHostQuery()->orderby('hosts.created_at' , 'desc');

            // check page type 

            $base_query = self::get_page_type_query($request, $base_query);

            $take = Setting::get('admin_take_count', 12);

            $skip = $request->skip ?: 0;

            $host_ids = $base_query->skip($skip)->take($take)->pluck("hosts.id");

            $host_ids = $host_ids ? $host_ids->toArray() : [];

            $hosts = HostRepo::host_list_response($host_ids, $request->id);

            return $hosts;

        }  catch( Exception $e) {

            Log::info($e->getMessage());

            return [];

        }

    }

    /**
     *
     * @method top_rated_hosts()
     *
     * @uses used to get the list of hosts based on the recently uploaded
     *
     * @created Vidhya R
     *
     * @updated Vidhya R
     *
     * @param integer $user_id
     *
     * @param integer $sub_profile_id
     *
     * @param integer $skip
     *
     * @return list of hosts
     */

    public static function top_rated_hosts($request) {

        try {

            $base_query = Host::VerifedHostQuery()->orderby('hosts.created_at' , 'desc');

            $base_query = self::get_page_type_query($request, $base_query);

            $take = Setting::get('admin_take_count', 12);

            $skip = $request->skip ?: 0;

            $host_ids = $base_query->skip($skip)->take($take)->pluck('hosts.id');

            $host_ids = $host_ids ? $host_ids->toArray() : [];

            $hosts = HostRepo::host_list_response($host_ids, $request->id);

            return $hosts;

        }  catch( Exception $e) {

            Log::info($e->getMessage());

            return [];

        }

    }    

    /**
     *
     * @method suggestions()
     *
     * @uses used to get the list of hosts based on the booked & search history
     *
     * @created Vidhya R
     *
     * @updated Vidhya R
     *
     * @param integer $user_id
     *
     * @param integer $skip
     *
     * @return list of hosts
     */

    public static function suggestions($request) {

        try {

            $base_query = Host::VerifedHostQuery()->orderby('hosts.created_at' , 'desc');

            $base_query = self::get_page_type_query($request, $base_query);

            $take = Setting::get('admin_take_count', 12);

            $skip = $request->skip ?: 0;

            $host_ids = $base_query->skip($skip)->take($take)->pluck('hosts.id');

            $host_ids = $host_ids ? $host_ids->toArray() : [];

            $hosts = HostRepo::host_list_response($host_ids, $request->id);

            return $hosts;

        }  catch( Exception $e) {

            Log::info($e->getMessage());

            return [];

        }

    }

    /**
     *
     * @method category_based_hosts()
     *
     * @uses used to get the list of hosts based on the category
     *
     * @created Vidhya R
     *
     * @updated Vidhya R
     *
     * @param integer $user_id
     *
     * @param integer $skip
     *
     * @return list of hosts
     */

    public static function category_based_hosts($request) {

        try {

            $base_query = Host::where('hosts.category_id', $request->category_id)->orderby('hosts.created_at' , 'desc');

            $base_query = self::get_page_type_query($request, $base_query);

            $take = Setting::get('admin_take_count', 12);

            $skip = $request->skip ?: 0;

            $host_ids = $base_query->skip($skip)->take($take)->pluck('hosts.id');

            $host_ids = $host_ids ? $host_ids->toArray() : [];

            $hosts = HostRepo::host_list_response($host_ids, $request->id);

            return $hosts;

        }  catch( Exception $e) {

            Log::info($e->getMessage());

            return [];

        }

    }

    /**
     *
     * @method sub_category_based_hosts()
     *
     * @uses used to get the list of hosts based on the sub category
     *
     * @created Vidhya R
     *
     * @updated Vidhya R
     *
     * @param integer $user_id
     *
     * @param integer $skip
     *
     * @return list of hosts
     */

    public static function sub_category_based_hosts($request) {

        try {

            $base_query = Host::VerifedHostQuery()->where('hosts.sub_category_id', $request->sub_category_id)->orderby('hosts.created_at' , 'desc');

            $base_query = self::get_page_type_query($request, $base_query);

            $take = Setting::get('admin_take_count', 12);

            $skip = $request->skip ?: 0;

            $host_ids = $base_query->skip($skip)->take($take)->pluck('hosts.id');

            $host_ids = $host_ids ? $host_ids->toArray() : [];

            $hosts = HostRepo::host_list_response($host_ids, $request->id);

            return $hosts;

        }  catch( Exception $e) {

            Log::info($e->getMessage());

            return [];

        }

    }

    /**
     * @method check_step1_status()
     *
     * @uses [check the step 1 is completed and update the status]
     *
     * @created vithya R
     *
     * @updated vithya R
     *
     * @param object $host_details
     *
     * @return boolean
     */
    
    public static function check_step1_status($host, $host_details) {


        if($host_details->min_guests && $host_details->max_guests && $host_details->total_guests && $host_details->total_bedrooms && $host_details->total_beds && $host_details->total_bathrooms && $host_details->bathroom_type && $host->category_id && $host->sub_category_id && $host->host_type) {

            $host_details->step1 = YES;
            $host_details->save();

            Log::info("Step1 status check - Passed");

        } else {

            Log::info("Step1 status check - else");
        }
    
    }

    /**
     * @method check_step2_status()
     *
     * @uses [check the step 1 is completed and update the status]
     *
     * @created vithya R
     *
     * @updated vithya R
     *
     * @param object $host_details
     *
     * @return boolean
     */
    
    public static function check_step2_status($host, $host_details) {

        // if($host->full_address && $host->street_details && $host->city && $host->state && $host->latitude && $host->longitude) {

        if($host->full_address && $host->latitude && $host->longitude) {

            $host_details->step2 = YES;

            $host_details->save();

            Log::info("Step2 status check - Passed");

        } else {

            Log::info("Step2 status check - else");
        }
    
    }

    /**
     * @method check_step3_status()
     *
     * @uses check the step 3 is completed and update the status]
     *
     * @created vithya R
     *
     * @updated vithya R
     *
     * @param object $host_details
     *
     * @return boolean
     */
    
    public static function check_step3_status($host, $host_details) {

        // Get the amenties details

        $amenties_ids = CommonQuestion::where('question_static_type', 'amenties')->where('is_required', YES)->where('status', APPROVED)->pluck('id')->toArray();

        // Check the questions filled

        $check_host_questions = HostQuestionAnswer::whereIn('common_question_id', $amenties_ids)->where('status', APPROVED)->count();

        if($check_host_questions == count($amenties_ids)) {

            $host_details->step3 = YES;

            $host_details->save();

            Log::info("Step3 status check - Passed");

        } else {

            Log::info("Step3 status check - else");
        }
    
    }

    /**
     * @method check_step4_status()
     *
     * @uses check the step 4 is completed and update the status]
     *
     * @created vithya R
     *
     * @updated vithya R
     *
     * @param object $host_details
     *
     * @return boolean
     */
    
    public static function check_step4_status($host, $host_details) {

        if($host->host_name && $host->description && $host->picture) {

            $host_details->step4 = YES;

            $host_details->save();

            Log::info("Step4 status check - Passed");

        } else {

            Log::info("Step4 status check - else");
        }
    
    }

    /**
     * @method check_step5_status()
     *
     * @uses check the step 4 is completed and update the status]
     *
     * @created vithya R
     *
     * @updated vithya R
     *
     * @param object $host_details
     *
     * @return boolean
     */
    
    public static function check_step5_status($host, $host_details) {

        if($host->checkin && $host->checkout && $host->min_days && $host->max_days) {

            $host_details->step5 = YES;

            $host_details->save();

            Log::info("Step3 status check - Passed");

        } else {

            Log::info("Step3 status check - else");
        }
    
    }

    /**
     * @method check_step6_status()
     *
     * @uses check the step 4 is completed and update the status]
     *
     * @created vithya R
     *
     * @updated vithya R
     *
     * @param object $host_details
     *
     * @return boolean
     */
    
    public static function check_step6_status($host, $host_details) {

        // if($host->base_price && $host->per_day && $host->per_week && $host->per_month) {
        if($host->per_day) {

            $host_details->step6 = YES;

            $host_details->save();

            Log::info("Step3 status check - Passed");

        } else {

            Log::info("Step3 status check - else");
        }
    
    }

    /**
     * @method check_step7_status()
     *
     * @uses check the step 4 is completed and update the status]
     *
     * @created vithya R
     *
     * @updated vithya R
     *
     * @param object $host_details
     *
     * @return boolean
     */
    
    public static function check_step7_status($host, $host_details) {

        // Check the host availability updated

        $check_availabilities = HostAvailability::where('host_id', $host->id)->count();

        // if($check_availabilities >= $host->min_days) {

            $host_details->step7 = YES;

            $host_details->save();

            // Log::info("Step7 status check - Passed");

        // } else {

            // Log::info("Step7 status check - else");
            
        // }
    
    }

    /**
    /**
     * @method check_step8_status()
     *
     * @uses check the step 4 is completed and update the status]
     *
     * @created vithya R
     *
     * @updated vithya R
     *
     * @param object $host_details
     *
     * @return boolean
     */
    
    public static function check_step8_status($host, $host_details) {

        // if($host->base_price && $host->per_day && $host->per_week && $host->per_month) {

            $host_details->step8 = YES;

            $host_details->save();

            Log::info("Step8 status check - Passed");

        // } else {

            // Log::info("Step3 status check - else");
        // }
    
    }

    /**
     *
     * @method get_page_type_query()
     *
     * @uses based on the page type, change the query
     *
     * @created Vidhya R
     *
     * @updated Vidhya R
     *
     * @param Request $request
     *
     * @param $base_query
     *
     * @return $base_query 
     */
    public static function get_page_type_query($request, $base_query) {

        if($request->api_page_type == API_PAGE_TYPE_HOME) {

            // No logics

        } elseif($request->api_page_type == API_PAGE_TYPE_CATEGORY) {

            $base_query = $base_query->where('hosts.category_id', $request->api_page_type_id);

        } elseif($request->api_page_type == API_PAGE_TYPE_SUB_CATEGORY) {

            $base_query = $base_query->where('hosts.sub_category_id', $request->api_page_type_id);

        } elseif($request->api_page_type == API_PAGE_TYPE_LOCATION) {

            $base_query = $base_query->where('hosts.service_location_id', $request->api_page_type_id);

        }

        return $base_query;

    }

    /**
     * @method generate_date_range()
     * 
     * @uses Creating date collection between two dates
     *
     * @param string since any date, time or datetime format
     * 
     * @param string until any date, time or datetime format
     * 
     * @param string step
     * 
     * @param string date of output format
     * 
     * @return array
     */
    public static function generate_date_range($year = "", $month = "", $step = '+1 day', $output_format = 'd/m/Y', $loops = 2) {

        $year = $year ?: date('Y');

        $month = $month ?: date('m');

        $data = [];

        for($current_loop = 0; $current_loop < $loops; $current_loop++) {

            // Get the start and end date of the months

            $month_start_date = Carbon::createFromDate($year, $month, 01)->format('Y-m-d');

            $no_of_days = Carbon::parse($month_start_date)->daysInMonth;

            $month_end_date = Carbon::createFromDate($year, $month, $no_of_days)->format('Y-m-d');

            $period = CarbonPeriod::create($month_start_date, $month_end_date);

            $dates = [];

            // Iterate over the period
            foreach ($period as $date) {
                $dates[] = $date->format('Y-m-d');
            }

            // Create object

            $loop_data = new \stdClass;;

            $loop_data->month = $month;

            $loop_data->year = $year;

            $loop_data->total_days = $no_of_days;

            $loop_data->dates = $dates;

            array_push($data, $loop_data);

            // Update the next loops

            if($loops > 1) {

                $check_date = Carbon::createFromDate($year, $month, 01)->addMonth(1)->day(01);

                $year = $check_date->year;

                $month = $check_date->month;
            }
        
        }

        return $data;
    }

    /**
     * @method check_host_availablity()
     *
     * @uses 
     *
     * @param string since any date, time or datetime format
     * 
     * @param string until any date, time or datetime format
     * 
     * @param string step
     * 
     * @param string date of output format
     * 
     * @return array
     */
    public static function check_host_availablity($checkin, $checkout, $host_id) {

        // Get the intervals between two dates

       $period = CarbonPeriod::create($checkin, $checkout);

       $blocked_dates = 0;

        // Iterate over the period
        foreach ($period as $date) {

            // Check the dates are available 

            $is_blocked = HostAvailability::where('host_id', $host_id)->whereDate('available_date', $date)->where('is_blocked_booking', YES)->count();

            $blocked_dates += $is_blocked;

            $dates[] = $date->format('Y-m-d');

        }

        return $is_host_available =  $blocked_dates == 0 ? YES : NO;        

    }

    public static function total_guests_calculate() {

    }

    /**
     * @method first_block()
     *
     * @uses used get categories/sub categories of the home page
     *
     * @created Vidhya R
     * 
     * @updated Vidhya R
     *
     * @param
     *
     * @return
     */

    public static function first_block($request) {

        $categories_data = []; 

        if($request->api_page_type == API_PAGE_TYPE_CATEGORY) {

            // Get the categories
            
            $categories = SubCategory::HomeResponse()->where('sub_categories.status' , APPROVED)->orderBy('sub_categories.name' , 'asc')->where('category_id', $request->api_page_type_id)->get();

            $categories_data['api_page_type'] = API_PAGE_TYPE_SUB_CATEGORY;

            $categories_data['is_see_all'] = YES;

        } elseif($request->api_page_type == API_PAGE_TYPE_HOME) {
            
            $categories = Category::HomeResponse()->where('categories.status' , APPROVED)->orderBy('categories.name' , 'asc')->get();

            $categories_data['api_page_type'] = API_PAGE_TYPE_CATEGORY;

            $categories_data['is_see_all'] = NO;

        }

        $categories_data['title'] = tr('what_we_can_find_you_help');

        $categories_data['description'] = "";

        $categories_data['api_page_type_id'] = $request->api_page_type_id ?: 0;

        // $categories_data['url_type'] = URL_TYPE_CATEGORY;

        // $categories_data['url_page_id'] = 0;

        $categories_data['data'] = $categories;

        return $categories_data;
    
    }

    public static function location_block($request) {

        $locations = HostHelper::locations_data($request);

        $location_data['title'] = tr('URL_TYPE_LOCATION');

        $location_data['description'] = "";

        $location_data['api_page_type'] = API_PAGE_TYPE_LOCATION;

        $location_data['api_page_type_id'] = $request->api_page_type_id ?: 0;

        $location_data['is_see_all'] = NO;

        // $location_data['url_type'] = URL_TYPE_LOCATION;

        // $location_data['url_page_id'] = 0;

        $location_data['data'] = $locations;

        return $location_data;
    
    }

    public static function host_policies($host_id) {

        // Get the policies questions

        $common_question_ids = CommonQuestion::where('question_static_type', 'rules')->where('status', APPROVED)->pluck('id as common_question_id');

        $host_question_answers = HostQuestionAnswer::where('host_id', $host_id)->whereIn('common_question_id', $common_question_ids)->select('common_question_id', 'answers as description')->get();

        foreach($host_question_answers as $host_question_answer) {

            $question_details = CommonQuestion::find($host_question_answer->common_question_id);

            $host_question_answer->title = $question_details ? $question_details->user_question : "";
        }

        return $host_question_answers;

    }

    public static function filter_options_others($request) {

        $data = [];

        // For web - host type & pricings will be added in main data

        if($request->device_type != DEVICE_WEB) {

            // | # | # | Host Types | # | # |

            $host_types_data = self::filter_options_host_type($request);

            array_push($data, $host_types_data);

            // | # | # | Host Types | # | # |

            // | # | # | Pricings | # | # |
            
            $pricings_data = self::filter_options_pricings($request);

            array_push($data, $pricings_data);

            // | # | # | Pricings | # | # |

        }

        // | # | # | Rooms and beds | # | # |

        $rooms_beds = new \stdClass;

        $rooms_beds->title = tr('SEARCH_OPTION_ROOMS_BEDS');

        $rooms_beds->description = "";

        $rooms_beds->search_type = SEARCH_OPTION_ROOMS_BEDS;

        $rooms_beds->search_key = "room_beds";

        $rooms_beds->type = INCREMENT_DECREMENT;

        $rooms_beds->is_group = YES;

        $rooms_beds->should_display_the_filter = YES;

        $rooms_beds_data = $rb_data = [];

        // Beds

        $rb_data['title'] = tr('beds'); 

        $rb_data['description'] = ''; 

        $rb_data['min'] = "0"; $rb_data['max'] = "16";

        $rb_data['search_key'] = 'total_beds';

        $rb_data['type'] = INCREMENT_DECREMENT;

        array_push($rooms_beds_data, $rb_data);

        // Bedrooms

        $rb_data = [];

        $rb_data['title'] = tr('bedrooms'); 

        $rb_data['description'] = ''; 

        $rb_data['min'] = "0"; $rb_data['max'] = "16";

        $rb_data['search_key'] = 'total_bedrooms';

        $rb_data['type'] = INCREMENT_DECREMENT;

        array_push($rooms_beds_data, $rb_data);

        // bathrooms

        $rb_data = []; 

        $rb_data['title'] = tr('bathrooms'); 

        $rb_data['description'] = ''; 

        $rb_data['min'] = "0"; $rb_data['max'] = "16";

        $rb_data['search_key'] = 'total_bathrooms';

        $rb_data['type'] = INCREMENT_DECREMENT;

        array_push($rooms_beds_data, $rb_data);

        $rooms_beds->data = $rooms_beds_data;

        // Update the main data

        array_push($data, $rooms_beds);

        // | # | # | Rooms and beds | # | # |


        // | # | # | Amenties | # | # |

        $amenties_question_ids = CommonQuestion::where('question_static_type', 'amenties')->where('status', APPROVED)->pluck('id');

        if($amenties_question_ids) {

            $amenties_answers = CommonQuestionAnswer::whereIn('common_question_id', $amenties_question_ids)->where('status', APPROVED)->orderBy('common_answer')->get();

            if($amenties_answers) {

                $amenties_answers_data = [];

                foreach ($amenties_answers as $key => $amenties_answer) {

                    $ameties_option_data = [];

                    $ameties_option_data['title'] = $amenties_answer->common_answer;

                    $ameties_option_data['description'] = "";

                    $ameties_option_data['search_value'] = "$amenties_answer->id";

                    array_push($amenties_answers_data, $ameties_option_data);
                
                }

                $amenties_data = new \stdClass;

                $amenties_data->title = tr('SEARCH_OPTION_AMENTIES');

                $amenties_data->search_type = SEARCH_OPTION_AMENTIES;

                $amenties_data->search_key = 'amenties';

                $amenties_data->should_display_the_filter = YES;

                $amenties_data->type = CHECKBOX;

                $amenties_data->data = $amenties_answers_data;

                array_push($data, $amenties_data);

            }

        }

        // | # | # | Amenties | # | # |


        // | # | # | Sub categories | # | # |

        $sub_category_base_query = SubCategory::orderBy('sub_categories.name', 'desc');

        if($request->category_id) {

            $sub_categories = $sub_category_base_query->where('category_id', $request->category_id)->first();
        }

        $sub_categories = $sub_category_base_query->get();

        if($sub_categories) {

            // initialize variables

            $sc_data = [];

            foreach ($sub_categories as $key => $sub_category_details) {

                $sc_option_data = [];

                $sc_option_data['title'] = $sub_category_details->name;

                $sc_option_data['description'] = $sub_category_details->description;

                $sc_option_data['search_value'] = "$sub_category_details->id";

                array_push($sc_data, $sc_option_data);

            }

            $sub_categories_data = new \stdClass;

            $sub_categories_data->title = tr('SEARCH_OPTION_SUB_CATEGORY');

            $sub_categories_data->search_type = SEARCH_OPTION_SUB_CATEGORY;

            $sub_categories_data->type = CHECKBOX;

            $sub_categories_data->search_key = 'sub_category_id';

            $sub_categories_data->should_display_the_filter = YES;

            $sub_categories_data->data = $sc_data;

            array_push($data, $sub_categories_data);
        
        }

        // | # | # | Sub categories | # | # |

        return $data;

    }

    public static function filter_guests($request) {

        $adults_data = $data = [];

        $adults_data['title'] = tr('adults');

        $adults_data['description'] = "";

        $adults_data['search_key'] = 'adults';

        array_push($data, $adults_data);

        $children_data = [];

        $children_data['title'] = tr('children');

        $children_data['description'] = "Ages 2 - 12";

        $children_data['search_key'] = 'children';

        array_push($data, $children_data);

        $infants_data = [];

        $infants_data['title'] = tr('infants');

        $infants_data['description'] = "Under 2";

        $infants_data['search_key'] = 'infants';

        array_push($data, $infants_data);

        return $data;

    }

    public static function filter_options_host_type($request) {

        $host_types_data = new \stdClass;

        $host_types_data->title = tr('SEARCH_OPTION_HOST_TYPE');

        $host_types_data->description = "";

        $host_types_data->search_type = SEARCH_OPTION_HOST_TYPE;

        $host_types_data->type = CHECKBOX;

        $host_types_data->search_key = 'host_type';

        $host_types_data->should_display_the_filter = YES;

        $host_types = self::get_host_types();

        $host_types_data->data = $host_types;

        return $host_types_data;

    }

    public static function filter_options_pricings($request) {

        $pricings_data = new \stdClass;

        $pricings_data->title = tr('SEARCH_OPTION_PRICE');

        $pricings_data->description = "";

        $pricings_data->search_type = SEARCH_OPTION_PRICE;

        $pricings_data->type = RANGE;

        $pricings_data->search_key = 'price';

        $pricings_data->start_key = "0.00";

        $pricings_data->end_key = "100000000.00";

        $pricings_data->should_display_the_filter = YES;

        $price_data['min_price'] = "0.00";

        $price_data['max_price'] = "100000000.00";

        $pricings_data->data = $price_data;

        return $pricings_data;

    }
    

    /**
     *
     * @method get_host_types()
     *
     * @uses get host types
     *
     * @created vithya R
     *
     * @updated vithya R
     *
     * @param
     *
     * @return 
     */

    public static function get_host_types($host_type = "") {

        $host_data[0]['title'] = tr('user_host_type_entire_place_title');
        $host_data[0]['description'] = tr('user_host_type_entire_place_description');
        $host_data[0]['search_value'] = HOST_ENTIRE;

        $host_data[1]['title'] = tr('user_host_type_private_title');
        $host_data[1]['description'] = tr('user_host_type_private_description');
        $host_data[1]['search_value'] = HOST_PRIVATE;

        $host_data[2]['title'] = tr('user_host_type_shared_title');
        $host_data[2]['description'] = tr('user_host_type_shared_description');
        $host_data[2]['search_value'] = HOST_SHARED;

        return $host_data;
    
    }

    /**
     *
     * @method check_host_steps()
     *
     * @uses based on the input check the host steps are completed
     *
     * @created vithya R
     *
     * @updated vithya R
     *
     * @param object $host
     * 
     * @param object $host_details
     *
     * @return 
     */
    
    public static function check_host_steps($step = HOST_STEP_WEB, $host, $host_details, $device_type = DEVICE_ANDROID) {

        Log::info("check_host_steps - - - - ".$device_type);
       
        switch ($step) {

            // Host details (Structure & rooms)
            case HOST_STEP_1: 
                HostHelper::check_step1_status($host, $host_details);
                break;

            // Location update
            case HOST_STEP_2:
                HostHelper::check_step2_status($host, $host_details);
                break;

            // Amenties
            case HOST_STEP_3:
                HostHelper::check_step3_status($host, $host_details);
                break;

            // Title, Description
            case HOST_STEP_4:
                HostHelper::check_step4_status($host, $host_details);
                break;

            // Other Questions
            case HOST_STEP_5:
                HostHelper::check_step5_status($host, $host_details);
                break;

            // Pricing
            case HOST_STEP_6:
                HostHelper::check_step6_status($host, $host_details);
                break;

            // HOST_STEP_7 - Availability
            case HOST_STEP_7:
                HostHelper::check_step7_status($host, $host_details);
                break;

            case HOST_STEP_8:
                HostHelper::check_step8_status($host, $host_details);
                break;
            
            default:

                if($device_type == DEVICE_WEB) {
                    HostHelper::check_step1_status($host, $host_details);
                    HostHelper::check_step2_status($host, $host_details);
                    HostHelper::check_step3_status($host, $host_details);
                    HostHelper::check_step4_status($host, $host_details);
                    HostHelper::check_step5_status($host, $host_details);
                    HostHelper::check_step6_status($host, $host_details);
                    HostHelper::check_step7_status($host, $host_details);
                    HostHelper::check_step8_status($host, $host_details);
                } else {

                    // Do nothing
                }

                break;
        }

        return true;
    } 
    
}
