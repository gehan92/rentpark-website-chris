<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Log, Validator, Exception, DB;

use App\User, App\Provider;

use App\Category, App\SubCategory;

use App\StaticPage;

use App\ChatMessage;

use App\ServiceLocation;

use App\Helpers\Helper;

use Setting;

class ApplicationController extends Controller {

	/**
     * @method static_pages()
     *
     * @uses used to display the static page for mobile devices
     *
     * @created Vidhya R
     *
     * @updated Vidhya R
     *
     * @param string $page_type 
     *
     * @return reidrect to the view page
     */

    public function static_pages($page_type = 'terms') {

        $page_details = StaticPage::where('unique_id' , $page_type)->first();

        return view('static_pages.view')->with('page_details', $page_details);

    }  


    /**
     * @method static_pages_api()
     *
     * @uses used to get the pages
     *
     * @created Vidhya R 
     *
     * @updated Vidhya R
     *
     * @param - 
     *
     * @return JSON Response
     */

    public function static_pages_api(Request $request) {

        if($request->page_type) {

            $static_page = StaticPage::where('type' , $request->page_type)
                                ->where('status' , APPROVED)
                                ->select('id as page_id' , 'title' , 'description','type as page_type', 'status' , 'created_at' , 'updated_at')
                                ->first(); 

            $response_array = ['success' => true ,'data' => $static_page];

        } else {

            $static_pages = StaticPage::where('status' , APPROVED)->orderBy('id' , 'asc')
                                ->select('id as page_id' , 'title' , 'description','type as page_type', 'status' , 'created_at' , 'updated_at')
                                ->orderBy('title', 'asc')
                                ->get();

            $response_array = ['success' => true ,'data' => $static_pages ? $static_pages->toArray(): []];

        }
        
        return response()->json($response_array , 200);

    }

    /**
     * @method static_pages_api()
     *
     * @uses used to get the pages
     *
     * @created Vidhya R 
     *
     * @updated Vidhya R
     *
     * @param - 
     *
     * @return JSON Response
     */

    public function static_pages_web(Request $request) {
// $static_page = StaticPage::where('unique_id' , $request->unique_id)
        $static_page = StaticPage::where('type' , $request->unique_id)
                            ->where('status' , APPROVED)
                            ->select('id as page_id' , 'title' , 'description','type as page_type', 'status' , 'created_at' , 'updated_at')
                            ->first();

        $response_array = ['success' => true , 'data' => $static_page];

        

        return response()->json($response_array , 200);

    }

    /**
     * @method get_sub_categories()
     * 
     * @uses - Used to get subcategory list based on the selected category
     *
     * @created vidhya R
     *
     * @updated vidhya R
     * 
     * @param 
     *
     * @return JSON Response
     *
     */

    public function get_sub_categories(Request $request) {
        
        $category_id = $request->category_id;

        $sub_categories = SubCategory::where('category_id', '=', $category_id)
                            ->where('status' , APPROVED)
                            ->orderBy('name', 'asc')
                            ->get();

        $view_page = view('admin.others._sub_categories_list')->with('sub_categories' , $sub_categories)->render();

        $response_array = ['success' =>  true , 'view' => $view_page];

        return response()->json($response_array , 200);
    
    }

    /**
     * @method chat_messages_save()
     * 
     * @uses - To save the chat message.
     *
     * @created vidhya R
     *
     * @updated vidhya R
     * 
     * @param 
     *
     * @return No return response.
     *
     */

    public function chat_messages_save(Request $request) {

        try {

            Log::info("message_save".print_r($request->all() , true));

            $validator = Validator::make($request->all(), [
                'user_id' => 'required|integer',
                'provider_id' => 'required|integer',
                'type' => 'required|in:up,pu',
                'message' => 'required',
                'host_id' => 'integer',
                'booking_id' => 'integer'
            ]);

            if($validator->fails()) {

                $error = implode(',', $validator->messages()->all());

                $response_array = ['success' => false, 'error' => $error, 'error_code' => 101];

                return response()->json($response_array, 200);
            }

            $type = 'chat';
            
            $message = $request->message;

            $chat_data = ['provider_id' => "$request->provider_id" , 'user_id' => "$request->user_id" ];

            if($request->type == 'up') {

                $push_send_status = 1;

                // Get Push Status 

                $check_push_status = ChatMessage::where('provider_id' , $request->provider_id)->where('type' , 'up')->orderBy('updated_at' , 'desc')->first();

                if($check_push_status) {
                    $push_send_status = $check_push_status->delivered ? 0 : 1;
                }

                if($push_send_status) {

                    $title = tr('new_message_from_user');

                    // $this->dispatch(new sendPushNotification($request->provider_id,PROVIDER,PUSH_USER_CHAT,$title,$message ,  "" , $chat_data)); 
                }
      
            }

            if($request->type == 'pu' || $request->type == 'hu') {

                $push_send_status = 1;

                if($request->type == 'pu'){
                    $check_push_status = ChatMessage::where('user_id' , $request->user_id)->where('provider_id', $request->provider_id)->where('type' , 'pu')->orderBy('updated_at' , 'desc')->first();
                } else {
                    $check_push_status = ChatMessage::where('user_id' , $request->user_id)->where('host_id', $request->host_id)->where('type' , 'pu')->orderBy('updated_at' , 'desc')->first();
                }

                if($check_push_status) {
                    $push_send_status = $check_push_status->delivered ? 0 : 1;
                }

                if($push_send_status) {

                    $title = tr('new_message_from_provider');

                    // $this->dispatch( new sendPushNotification($request->user_id, USER,PUSH_PROVIDER_CHAT, $title, $message , "" , $chat_data));
                }
            
            }

            if($request->type == 'uh') {

                $push_send_status = 1;

                // Get Push Status 

                $check_push_status = ChatMessage::where('host_id' , $request->host_id)->where('type' , 'uh')->orderBy('updated_at' , 'desc')->first();

                if($check_push_status) {
                    $push_send_status = $check_push_status->delivered ? 0 : 1;
                }

                if($push_send_status) {

                    $title = tr('new_message_from_user');

                    // $this->dispatch(new sendPushNotification($request->host_id,PROVIDER,PUSH_USER_CHAT,$title,$message ,  "" , $chat_data)); 
                }
            
            }

            $chat_message_details = ChatMessage::create($request->all());

            return $this->sendResponse("", "", $chat_message_details);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode ?: 101);
        }
    
    }

    /**
     * @method chat_messages_update_status()
     * 
     * @uses - To check the status of the message whether delivered or not. 
     *
     * @created vidhya R
     *
     * @updated vidhya R
     * 
     * @param 
     *
     * @return No Response
     *
     */

    public function chat_messages_update_status(Request $request) {

        // Need to update the user status

        if($request->type == 'pu') {

            ChatMessage::where('user_id' , $request->user_id)->where('provider_id' , $request->provider_id)->where('type' , 'pu')->update(['delivered' => 1]);

        } 

        if($request->type == 'up') {

            ChatMessage::where('user_id' , $request->user_id)->where('provider_id' , $request->provider_id)->where('type' , 'up')->update(['delivered' => 1]);

        }

    }

    /**
     * @method categories()
     *
     * @uses used get the categories lists
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param 
     *
     * @return response of details
     */

    public function categories(Request $request) {

        try {

            $categories = Category::CommonResponse()->has('approvedSubCategories')->where('categories.status' , APPROVED)->orderBy('name' , 'asc')->get();

            foreach ($categories as $key => $category_details) {

                $category_details->api_page_type = API_PAGE_TYPE_CATEGORY;

                $category_details->api_page_type_id = $category_details->category_id;
            }

            return $this->sendResponse("", "", $categories);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode ?: 101);
        }

    }

    /**
     * @method sub_categories()
     *
     * @uses used get the sub_categories lists
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param 
     *
     * @return response of details
     */

    public function sub_categories(Request $request) {

        try {

            $sub_categories = SubCategory::CommonResponse()
                    ->where('category_id', $request->category_id)
                    ->where('sub_categories.status' , APPROVED)
                    ->orderBy('sub_categories.name' , 'asc')
                    ->get();

            return $this->sendResponse("", "", $sub_categories);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }


    /**
     * @method service_locations()
     *
     * @uses used get the service_locations lists
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param 
     *
     * @return response of details
     */

    public function service_locations(Request $request) {

        try {

            $service_locations = ServiceLocation::CommonResponse()->where('service_locations.status' , APPROVED)->orderBy('service_locations.name' , 'asc')->get();

            $response_array = ['success' => true, 'data' => $service_locations];

            return $this->sendResponse("", "", $service_locations);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }

    /**
     * @method list_of_constants()
     *
     * @uses used get the list_of_constants lists
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param 
     *
     * @return response of details
     */

    public function list_of_constants(Request $request) {

        $host_steps = [
                'HOST_STEPS' => HOST_STEPS, 
                'HOST_STEP_0' => HOST_STEP_0, 
                'HOST_STEP_1' => HOST_STEP_1, 
                'HOST_STEP_2' => HOST_STEP_2, 
                'HOST_STEP_3' => HOST_STEP_3, 
                'HOST_STEP_4' => HOST_STEP_4, 
                'HOST_STEP_5' => HOST_STEP_5, 
                'HOST_STEP_6' => HOST_STEP_6, 
                'HOST_STEP_7' => HOST_STEP_7, 
                'HOST_STEP_8' => HOST_STEP_8, 
                'HOST_STEP_COMPLETE' => HOST_STEP_COMPLETE
            ];

        $page_types = [
                'API_PAGE_TYPE_HOME' => API_PAGE_TYPE_HOME,
                'API_PAGE_TYPE_CATEGORY' => API_PAGE_TYPE_CATEGORY,
                'API_PAGE_TYPE_SUB_CATEGORY' => API_PAGE_TYPE_SUB_CATEGORY,
                'API_PAGE_TYPE_SERVICE_LOCATION' => API_PAGE_TYPE_SERVICE_LOCATION,
                'API_PAGE_TYPE_SEE_ALL' => API_PAGE_TYPE_SEE_ALL,    
            ];

        $url_types = [
                'URL_TYPE_CATEGORY' => URL_TYPE_CATEGORY,
                'URL_TYPE_SUB_CATEGORY' => URL_TYPE_SUB_CATEGORY,
                'URL_TYPE_LOCATION' => URL_TYPE_LOCATION,
                'URL_TYPE_TOP_RATED' => URL_TYPE_TOP_RATED,
                'URL_TYPE_WISHLIST' => URL_TYPE_WISHLIST,
                'URL_TYPE_RECENT_UPLOADED' => URL_TYPE_RECENT_UPLOADED,
                'URL_TYPE_SUGGESTIONS' => URL_TYPE_SUGGESTIONS,
            ];

        $input_types = [

            'DROPDOWN' => DROPDOWN,
            'CHECKBOX' => CHECKBOX,
            'RADIO' => RADIO,
            'SPINNER' => SPINNER,
            'SPINNER_CALL_SUB_CATEGORY' => SPINNER_CALL_SUB_CATEGORY,
            'SWITCH' => 'SWITCH',
            'RANGE' => RANGE,
            'AVAILABILITY_CALENDAR' => AVAILABILITY_CALENDAR,
            'ABOUT_HOST_SPACE' => ABOUT_HOST_SPACE,
            'INPUT' => INPUT,
            'INPUT_NUMBER' => INPUT_NUMBER,
            'INPUT_TEXT' => INPUT_TEXT,
            'INPUT_GOOGLE_PLACE_SEARCH' => INPUT_GOOGLE_PLACE_SEARCH,
            'MAP_VIEW' => MAP_VIEW,
            'DATE' => DATE,
            'INCREMENT_DECREMENT' => INCREMENT_DECREMENT,
            'UPLOAD' => UPLOAD,
            'UPLOAD_SINGLE' => UPLOAD_SINGLE,
            'UPLOAD_MULTIPLE' => UPLOAD_MULTIPLE,

            ];

        return view('admin.constants')->with(compact('host_steps', 'page_types', 'url_types', 'input_types'));

    }

    /**
     * @method email_verify()
     *
     * @uses To verify the email from user and provider.  
     *
     * @created Bhawya
     *
     * @updated Bhawya
     *
     * @param -
     *
     * @return JSON RESPONSE
     */

    public function email_verify(Request $request) {

        if($request->user_id) {

            $user_details = User::find($request->user_id);

            if(!$user_details) {

                return redirect()->away(Setting::get('frontend_url'))->with('flash_error',tr('user_details_not_found'));
            } 

            if($user_details->is_verified == USER_EMAIL_VERIFIED) {

                return redirect()->away(Setting::get('frontend_url'))->with('flash_success' ,tr('user_verify_success'));
            }

            $response = Helper::check_email_verification($request->verification_code , $user_details->id, $error, USER);
            
            if($response) {

                $user_details->is_verified = USER_EMAIL_VERIFIED;       

                $user_details->save();

            } else {

                return redirect()->away(Setting::get('frontend_url'))->with('flash_error' , $error);
            }

        } else {

            $provider_details = Provider::find($request->provider_id);

            if(!$provider_details) {

                return redirect()->away(Setting::get('frontend_url'))->with('flash_error' , tr('provider_details_not_found'));
            }

            if($provider_details->is_verified) {
                return redirect()->away(Setting::get('frontend_url'))->with('flash_success' ,tr('provider_verify_success'));
            }

            $response = Helper::check_email_verification($request->verification_code , $provider_details->id, $error, PROVIDER);

            if($response) {

                $provider_details->is_verified = PROVIDER_EMAIL_VERIFIED;
                
                $provider_details->save();

            } else {

                return redirect()->away(Setting::get('frontend_url'))->with('flash_error' , $error);
            }

        }

        return redirect()->away(Setting::get('frontend_url'));
    
    }

    /**
     * @method cron_bookings_not_checkin_cancel()
     *
     * @uses   
     *
     * @created Vidhya
     *
     * @updated Vidhya
     *
     * @param -
     *
     * @return JSON RESPONSE
     */
    
    public function cron_bookings_not_checkin_cancel(Request $request) {

        // Get booking created and not checked in and greater than (25mins) checkin time

        $checkin_status = [BOOKING_DONE_BY_USER];

        $bookings = Booking::whereIn('status', $checkin_status)->get();

        foreach ($bookings as $key => $booking_details) {

            $booking_details->status = BOOKING_CANCELLED_BY_USER;

            $booking_details->save();

            // Send mail notification to user & provider 

            // Send push notification to user & provider

            // Send bell notification to user & provider
        }

    }


}
