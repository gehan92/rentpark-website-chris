<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Repositories\HostRepository as HostRepo;

use App\Helpers\Helper, App\Helpers\HostHelper;

use App\Http\Resources\HostCollection as HostCollection;

use App\Repositories\BookingRepository as BookingRepo;

use App\Repositories\PushNotificationRepository as PushRepo;

use Carbon\Carbon;

use Carbon\CarbonPeriod;

use DB, Log, Hash, Validator, Exception, Setting;

use App\Booking, App\BookingChat, App\BookingPayment;

use App\BookingProviderReview, App\BookingUserReview;

use App\Category, App\SubCategory, App\ServiceLocation;

use App\ChatMessage;

use App\CommonQuestion, App\CommonQuestionAnswer, App\CommonQuestionGroup;

use App\Host, App\HostDetails, App\HostAvailability, App\HostGallery, App\HostInventory, App\HostQuestionAnswer;

use App\Lookups, App\StaticPage;

use App\Provider, App\ProviderDetails;

use App\User, App\UserCard, App\Wishlist;

use App\BellNotification;

use App\UserVehicle;

use App\Mail\ForgotPasswordMail, App\Mail\WelcomeMail;

use App\Jobs\BellNotificationJob, App\Jobs\SendEmailJob;

use App\UserBillingInfo;

class UserApiController extends Controller {

    protected $loginUser;

    protected $skip, $take, $timezone, $currency, $push_notification_status, $device_type;

	public function __construct(Request $request) {

        Log::info(url()->current());

        Log::info("Request Data".print_r($request->all(), true));

        $this->loginUser = User::CommonResponse()->find($request->id);

        $this->skip = $request->skip ?: 0;

        $this->take = $request->take ?: (Setting::get('admin_take_count') ?: TAKE_COUNT);

        $this->currency = Setting::get('currency', '$');

        // $this->timezone = $this->loginUser->timezone ?? "America/New_York";
        $this->timezone = "";

        $this->push_notification_status = $this->loginUser->push_notification_status ?? 0;

        $this->device_type = $this->loginUser->device_type ?? DEVICE_WEB;
    }

    /**
     * @method register()
     *
     * @uses Registered user can register through manual or social login
     * 
     * @created Vithya R 
     *
     * @updated Vithya R
     *
     * @param Form data
     *
     * @return Json response with user details
     */
    public function register(Request $request) {

        try {

            DB::beginTransaction();

            // Validate the common and basic fields

            $basic_validator = Validator::make($request->all(),
                [
                    'device_type' => 'required|in:'.DEVICE_ANDROID.','.DEVICE_IOS.','.DEVICE_WEB,
                    'device_token' => 'required',
                    'login_by' => 'required|in:manual,facebook,google',
                ]
            );

            if($basic_validator->fails()) {

                $error = implode(',', $basic_validator->messages()->all());

                throw new Exception($error , 101);

            }

            $allowed_social_logins = ['facebook','google'];

            if(in_array($request->login_by,$allowed_social_logins)) {

                // validate social registration fields

                $social_validator = Validator::make($request->all(),
                            [
                                'social_unique_id' => 'required',
                                'name' => 'required|max:255|min:2',
                                'email' => 'required|email|max:255',
                                'mobile' => 'digits_between:6,13',
                                'picture' => '',
                                'gender' => 'in:male,female,others',
                            ]
                        );

                if($social_validator->fails()) {

                    $error = implode(',', $social_validator->messages()->all());

                    throw new Exception($error , 101);

                }

            } else {

                // Validate manual registration fields

                $manual_validator = Validator::make($request->all(),
                    [
                        'name' => 'required|max:255',
                        'email' => 'required|email|max:255|min:2',
                        'password' => 'required|min:6',
                        'picture' => 'mimes:jpeg,jpg,bmp,png',
                    ]
                );

                // validate email existence

                $email_validator = Validator::make($request->all(),
                    [
                        'email' => 'unique:users,email',
                    ]
                );

                if($manual_validator->fails()) {

                    $error = implode(',', $manual_validator->messages()->all());

                    throw new Exception($error , 101);
                    
                } else if($email_validator->fails()) {

                	$error = implode(',', $email_validator->messages()->all());

                    throw new Exception($error , 101);

                } 

            }

            $user_details = User::where('email' , $request->email)->first();

            $send_email = NO;

            // Creating the user

            if(!$user_details) {

                $user_details = new User;

                register_mobile($request->device_type);

                $user_details->picture = asset('placeholder.jpg');

                $user_details->registration_steps = 1;

                $send_email = YES;

            } else {
                
                if(in_array($user_details->status , [USER_PENDING , USER_DECLINED])) {

                    throw new Exception(Helper::error_message(1000) , 1000);
                
                }

            }            

            if($request->has('name')) {

                $user_details->name = $request->name;

            }

            if($request->has('email')) {

                $user_details->email = $request->email;

            }

            if($request->has('mobile')) {

                $user_details->mobile = $request->mobile;

            }

            if($request->has('password')) {

                $user_details->password = Hash::make($request->password ?: "123456");

            }

            $user_details->gender = $request->has('gender') ? $request->gender : "male";

            $user_details->payment_mode = $request->payment_mode ?: $user_details->payment_mode;

            if(check_demo_login($user_details->email, $user_details->token)) {

                $user_details->token = Helper::generate_token();

            }

            $user_details->token_expiry = Helper::generate_token_expiry();

            $user_details->device_type = $request->device_type ?: DEVICE_WEB;

            $user_details->login_by = $request->login_by ?: 'manual';

            $user_details->social_unique_id = $request->social_unique_id ?: '';

            // Upload picture

            if($request->login_by == "manual") {

                if($request->hasFile('picture')) {

                    $user_details->picture = Helper::upload_file($request->file('picture') , PROFILE_PATH_USER);

                }

            } else {

                $user_details->is_verified = USER_EMAIL_VERIFIED; // Social login

                $user_details->picture = $request->picture ?: $user_details->picture;

            }   

            if($user_details->save()) {

                // Update the device token

                $check_device_exist = User::where('id', '!=', $user_details->id)->where('device_token', $request->device_token)->first();

                if($check_device_exist) {

                    $check_device_exist->device_token = "";

                    $check_device_exist->save();
                }

                $user_details->device_token = $request->device_token ?: "";

                $user_details->save();

                // send welcome email to the new user:

                if($send_email) {

                    if($user_details->login_by == 'manual') {

                        $user_details->password = $request->password;
    
                        $email_data['subject'] = tr('user_welcome_title').' '.Setting::get('site_name');

                        $email_data['page'] = "emails.users.welcome";

                        $email_data['data'] = $user_details;

                        $email_data['email'] = $user_details->email;

                        $this->dispatch(new SendEmailJob($email_data));

                    }

                }

                if(in_array($user_details->status , [USER_DECLINED , USER_PENDING])) {
                
                    $response = ['success' => false , 'error' => Helper::error_message(1000) , 'error_code' => 1000];

                    DB::commit();

                    return response()->json($response, 200);
               
                }

                if($user_details->is_verified == USER_EMAIL_VERIFIED) {

                    counter(); // For site analytics. Don't remove

                	$data = User::CommonResponse()->find($user_details->id);

                    $response_array = ['success' => true, 'message' => "Welcome ".$data->name, 'data' => $data];

                } else {

                    $response_array = ['success' => false, 'error' => Helper::error_message(1001), 'error_code'=>1001];

                    DB::commit();

                    return response()->json($response_array, 200);

                }

            } else {

                throw new Exception(Helper::error_message(103), 103);

            }

            DB::commit();

            return response()->json($response_array, 200);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());

        }
   
    }

    /**
     * @method login()
     *
     * @uses Registered user can login using their email & password
     * 
     * @created Vithya R 
     *
     * @updated Vithya R
     *
     * @param object $request - User Email & Password
     *
     * @return Json response with user details
     */
    public function login(Request $request) {

        try {

            DB::beginTransaction();

            $basic_validator = Validator::make($request->all(),
                [
                    'device_token' => 'required',
                    'device_type' => 'required|in:'.DEVICE_ANDROID.','.DEVICE_IOS.','.DEVICE_WEB,
                    'login_by' => 'required|in:manual,facebook,google',
                ]
            );

            if($basic_validator->fails()){

                $error = implode(',', $basic_validator->messages()->all());

                throw new Exception($error , 101);

            }

            /** Validate manual login fields */

            $manual_validator = Validator::make($request->all(),
                [
                    'email' => 'required|email',
                    'password' => 'required',
                ]
            );

            if($manual_validator->fails()) {

                $error = implode(',', $manual_validator->messages()->all());

            	throw new Exception($error , 101);

            }

            $user_details = User::where('email', '=', $request->email)->first();

            $email_active = DEFAULT_TRUE;

            // Check the user details 

            if(!$user_details) {

            	throw new Exception(Helper::error_message(1002), 1002);

            }

            // check the user approved status

            if($user_details->status != USER_APPROVED) {

            	throw new Exception(Helper::error_message(1000), 1000);

            }

            if(Setting::get('is_account_email_verification') == YES) {

                if(!$user_details->is_verified) {

                    Helper::check_email_verification("" , $user_details->id, $error,USER);

                    $email_active = DEFAULT_FALSE;

                }

            }

            if(!$email_active) {

    			throw new Exception(Helper::error_message(1001), 1001);
            }

            if(Hash::check($request->password, $user_details->password)) {

                // Generate new tokens

                if(check_demo_login($user_details->email, $user_details->token)) {

                    $user_details->token = Helper::generate_token();

                }

                $user_details->token_expiry = Helper::generate_token_expiry();
                
                // Update the device token

                $check_device_exist = User::where('id', '!=', $user_details->id)->where('device_token', $request->device_token)->first();

                if($check_device_exist) {

                    $check_device_exist->device_token = "";

                    $check_device_exist->save();
                }

                $user_details->device_token = $request->device_token ?: $user_details->device_token;

                $user_details->device_type = $request->device_type ?: $user_details->device_type;

                $user_details->login_by = $request->login_by ? $request->login_by : $user_details->login_by;

                $user_details->save();

                counter(); // For site analytics. Don't remove

                DB::commit();

                $data = User::CommonResponse()->find($user_details->id);

                return $this->sendResponse(Helper::success_message(101), 101, $data);

            } else {

				throw new Exception(Helper::error_message(102), 102);
                
            }

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());

        }
    
    }
 
    /**
     * @method forgot_password()
     *
     * @uses If the user forgot his/her password he can hange it over here
     *
     * @created Vithya R 
     *
     * @updated Vithya R
     *
     * @param object $request - Email id
     *
     * @return send mail to the valid user
     */
    
    public function forgot_password(Request $request) {

        try {

            DB::beginTransaction();

            // Check email configuration and email notification enabled by admin

            if(Setting::get('is_email_notification') != YES || envfile('MAIL_USERNAME') == "" || envfile('MAIL_PASSWORD') == "" ) {

                throw new Exception(Helper::error_message(106), 106);
                
            }
            
            $validator = Validator::make($request->all(),
                [
                    'email' => 'required|email|exists:users,email',
                ],
                [
                    'exists' => 'The :attribute doesn\'t exists',
                ]
            );

            if($validator->fails()) {
                
                $error = implode(',',$validator->messages()->all());
                
                throw new Exception($error , 101);
            
            }

            $user_details = User::where('email' , $request->email)->first();

            if(!$user_details) {

                throw new Exception(Helper::error_message(1002), 1002);
            }

            if($user_details->login_by != "manual") {

                throw new Exception(Helper::error_message(116), 116);
                
            }

            // check email verification

            if($user_details->is_verified == USER_EMAIL_NOT_VERIFIED) {

                throw new Exception(Helper::error_message(1008), 1008);
            }

            // Check the user approve status

            if(in_array($user_details->status , [USER_DECLINED , USER_PENDING])) {
                throw new Exception(Helper::error_message(1000), 1000);
            }

            $new_password = Helper::generate_password();

            $user_details->password = Hash::make($new_password);

            $email_data['subject'] =  Setting::get('site_name').' '.tr('forgot_email_title');

            $email_data['page'] = "emails.users.forgot-password";

            $email_data['email'] = $user_details->email;

            $email_data['data'] = $new_password;

            $this->dispatch(new SendEmailJob($email_data));


            if(!$user_details->save()) {

                throw new Exception(Helper::error_message(103), 103);

            }

            DB::commit();

            $response_array = ['success' => true , 'message' => Helper::success_message(102)];

            return response()->json($response_array, 200);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }
    
    }

    /**
     * @method change_password()
     *
     * @uses To change the password of the user
     *
     * @created Vithya R 
     *
     * @updated Vithya R
     *
     * @param object $request - Password & confirm Password
     *
     * @return json response of the user
     */
    public function change_password(Request $request) {

        try {

            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                    'password' => 'required|confirmed|min:6',
                    'old_password' => 'required|min:6',
                ]);

            if($validator->fails()) {
                
                $error = implode(',',$validator->messages()->all());
               
                throw new Exception($error , 101);
           
            }

            $user_details = User::find($request->id);

            if(!$user_details) {

                throw new Exception(Helper::error_message(1002), 1002);
            }

            if($user_details->login_by != "manual") {

                throw new Exception(Helper::error_message(119), 119);
                
            }

            if(Hash::check($request->old_password,$user_details->password)) {

                $user_details->password = Hash::make($request->password);
                
                if($user_details->save()) {

                    DB::commit();

                    return $this->sendResponse(Helper::success_message(104), $success_code = 104, $data = []);
                
                } else {

                    throw new Exception(Helper::error_message(103), 103);   
                }

            } else {

                throw new Exception(Helper::error_message(108) , 108);
            }

            

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());

        }

    }

    /**
     * @method update_billing_info()
     *
     * @uses Update the Account details
     *
     * @created Bhawya
     *
     * @updated Bhawya
     *
     * @param object $request - Account Details
     *
     * @return json response of the user
     */
    public function update_billing_info(Request $request) {

        try {

            DB::beginTransaction();

            $user_billing_info = UserBillingInfo::where('user_id',$request->id)->first() ?? new UserBillingInfo;

            $user_billing_info->user_id = $request->id;

            $user_billing_info->account_name = $request->account_name ?? "";

            $user_billing_info->paypal_email = $request->paypal_email ?? "";

            $user_billing_info->account_no = $request->account_no ?? "";

            $user_billing_info->route_no = $request->route_no ?? "";

            if($user_billing_info->save()) {

                DB::commit();

                $data = UserBillingInfo::find($user_billing_info->id);

                return $this->sendResponse(Helper::success_message(222), $success_code = 222, $data);
                
            } else {

                throw new Exception(Helper::error_message(228), 228);   
            }

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());

        }

    }

    /**
     * @method billing_info()
     *
     * @uses View the Account details
     *
     * @created Bhawya
     *
     * @updated Bhawya
     *
     * @param object $request - Account Details
     *
     * @return json response of the user
     */
    public function billing_info(Request $request) {

        try {

            $user_billing_info = UserBillingInfo::where('user_id',$request->id)->select('id as user_billing_info_id' , 'account_name' , 'paypal_email' ,'account_no', 'route_no' )->first();
            
            $data = $user_billing_info ?? [];

            return $this->sendResponse($message = "", $success_code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());

        }
    
    }
    /** 
     * @method profile()
     *
     * @uses To display the user details based on user  id
     *
     * @created Vithya R 
     *
     * @updated Vithya R
     *
     * @param object $request - User Id
     *
     * @return json response with user details
     */

    public function profile(Request $request) {

        try {

            $user_details = User::where('id' , $request->id)->CommonResponse()->first();

            if(!$user_details) { 

                throw new Exception(Helper::error_message(1002) , 1002);
            }

            $card_last_four_number = "";

            if($user_details->user_card_id) {

                $card = UserCard::find($user_details->user_card_id);

                if($card) {

                    $card_last_four_number = $card->last_four;

                }

            }

            $data = $user_details->toArray();

            $data['card_last_four_number'] = $card_last_four_number;

            return $this->sendResponse($message = "", $code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());

        }
    
    }
 
    /**
     * @method update_profile()
     *
     * @uses To update the user details
     *
     * @created Vithya R 
     *
     * @updated Vithya R
     *
     * @param objecct $request : User details
     *
     * @return json response with user details
     */
    public function update_profile(Request $request) {

        try {

            DB::beginTransaction();
            
            $validator = Validator::make($request->all(),
                [
                    'name' => 'required|max:255',
                    'email' => 'email|unique:users,email,'.$request->id.'|max:255',
                    'mobile' => 'digits_between:6,13',
                    'picture' => 'mimes:jpeg,bmp,png',
                    'gender' => 'in:male,female,others',
                    'device_token' => '',
                    'description' => ''
                ]);

            if($validator->fails()) {

                // Error messages added in response for debugging

                $error = implode(',',$validator->messages()->all());
             
                throw new Exception($error , 101);
                
            }

            $user_details = User::find($request->id);

            if(!$user_details) { 

                throw new Exception(Helper::error_message(1002) , 1002);
            }

            $user_details->name = $request->name ?? $user_details->name;
            
            if($request->has('email')) {

                $user_details->email = $request->email;
            }

            $user_details->mobile = $request->mobile ?: $user_details->mobile;

            $user_details->gender = $request->gender ?: $user_details->gender;

            $user_details->description = $request->description ?: '';

            // Upload picture
            if($request->hasFile('picture')) {

                Helper::delete_file($user_details->picture, COMMON_FILE_PATH); // Delete the old pic

                $user_details->picture = Helper::upload_file($request->file('picture') , COMMON_FILE_PATH);

            }

            if($user_details->save()) {

            	$data = User::CommonResponse()->find($user_details->id);

                DB::commit();

                return $this->sendResponse(Helper::success_message(214), $code = 214, $data );

            } else {    

        		throw new Exception(Helper::error_message(103) , 103);
            }

        } catch (Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());

        }
   
    }

    /**
     * @method delete_account()
     * 
     * @uses Delete user account based on user id
     *
     * @created Vithya R 
     *
     * @updated Vithya R
     *
     * @param object $request - Password and user id
     *
     * @return json with boolean output
     */

    public function delete_account(Request $request) {

        try {

            DB::beginTransaction();

            $request->request->add([ 
                'login_by' => $this->loginUser ? $this->loginUser->login_by : "manual",
            ]);
            
            $validator = Validator::make($request->all(),
                [
                    'password' => 'required_if:login_by,manual',
                ], 
                [
                    'password.required_if' => 'The :attribute field is required.',
                ]);

            if($validator->fails()) {

                $error = implode(',',$validator->messages()->all());
             
                throw new Exception($error , 101);
                
            }

            $user_details = User::find($request->id);

            if(!$user_details) {

            	throw new Exception(Helper::error_message(1002), 1002);
                
            }

            // The password is not required when the user is login from social. If manual means the password is required

            if($user_details->login_by == 'manual') {

                if(!Hash::check($request->password, $user_details->password)) {

                    $is_delete_allow = NO ;

                    $error = Helper::error_message(108);
         
                    throw new Exception($error , 108);
                    
                }
            
            }

            if($user_details->delete()) {

                DB::commit();

                $message = Helper::success_message(103);

                return $this->sendResponse($message, $code = 103, $data = []);

            } else {

            	throw new Exception(Helper::error_message(205), 205);
            }

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }

	}

    /**
     * @method logout()
     *
     * @uses Logout the user
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param 
     * 
     * @return
     */
    public function logout(Request $request) {

        // @later no logic for logout

        return $this->sendResponse(Helper::success_message(106), 106);

    }

    /**
     * @method cards_list()
     *
     * @uses get the user payment mode and cards list
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param integer id
     * 
     * @return
     */

    public function cards_list(Request $request) {

        try {

            $user_cards = UserCard::where('user_id' , $request->id)->select('id as user_card_id' , 'customer_id' , 'last_four' ,'card_name', 'card_token' , 'is_default' )->get();

            $payment_modes = [];

            // $cod_data = [];

            // $cod_data['name'] = "COD";

            // $cod_data['payment_mode'] = COD;

            // $cod_data['is_default'] = $this->loginUser->payment_mode == COD ? YES : NO;

            // array_push($payment_modes , $cod_data);

            $card_data['name'] = "Card";

            $card_data['payment_mode'] = CARD;

            $card_data['is_default'] = $this->loginUser->payment_mode == CARD ? YES : NO;

            array_push($payment_modes , $card_data);

            $data['payment_modes'] = $payment_modes;   

            $data['cards'] = $user_cards ? $user_cards : []; 

            return $this->sendResponse($message = "", $code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());

        }
    
    }
    
    /**
     * @method cards_add()
     *
     * @uses Update the selected payment mode 
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param Form data
     * 
     * @return JSON Response
     */

    public function cards_add(Request $request) {

        try {

            if(Setting::get('stripe_secret_key')) {

                \Stripe\Stripe::setApiKey(Setting::get('stripe_secret_key'));

            } else {

                throw new Exception(Helper::error_message(133), 133);
            }
        
            $validator = Validator::make($request->all(),
                    [
                        'card_token' => 'required',
                    ]
                );

            if($validator->fails()) {

                $error = implode(',',$validator->messages()->all());
             
                throw new Exception($error , 101);

            }
            
            $user_details = User::find($request->id);

            if(!$user_details) {

                throw new Exception(Helper::error_message(1002), 1002);
                
            }

            DB::beginTransaction();

            // Get the key from settings table
            
            $customer = \Stripe\Customer::create([
                    "card" => $request->card_token,
                    "email" => $user_details->email,
                    "description" => "Customer for ".Setting::get('site_name'),
                ]);

            if($customer) {

                $customer_id = $customer->id;

                $card_details = new UserCard;

                $card_details->user_id = $request->id;

                $card_details->customer_id = $customer_id;

                $card_details->card_token = $customer->sources->data ? $customer->sources->data[0]->id : "";

                $card_details->card_name = $customer->sources->data ? $customer->sources->data[0]->brand : "";

                $card_details->last_four = $customer->sources->data[0]->last4 ? $customer->sources->data[0]->last4 : "";

                // Check is any default is available

                $check_card_details = UserCard::where('user_id',$request->id)->count();

                $card_details->is_default = $check_card_details ? 0 : 1;

                if($card_details->save()) {

                    if($user_details) {

                        $user_details->user_card_id = $check_card_details ? $user_details->user_card_id : $card_details->id;

                        $user_details->save();
                    }

                    $data = UserCard::where('id' , $card_details->id)->select('id as user_card_id' , 'customer_id' , 'last_four' ,'card_name', 'card_token' , 'is_default' )->first();

                    DB::commit();

                    return $this->sendResponse(Helper::success_message(105), $code = 105, $data);

                } else {

                    throw new Exception(Helper::error_message(117), 117);
                    
                }
           
            } else {

                throw new Exception(Helper::error_message(117) , 117);
                
            }

        } catch(Stripe_CardError | Stripe_InvalidRequestError | Stripe_AuthenticationError | Stripe_ApiConnectionError | Stripe_Error $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode() ?: 101);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode() ?: 101);
        }
   
    }

    /**
     * @method cards_delete()
     *
     * @uses delete the selected card
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param integer user_card_id
     * 
     * @return JSON Response
     */

    public function cards_delete(Request $request) {

        // Log::info("cards_delete");

        DB::beginTransaction();

        try {
    
            $user_card_id = $request->user_card_id;

            $validator = Validator::make($request->all(),
                [
                    'user_card_id' => 'required|integer|exists:user_cards,id,user_id,'.$request->id,
                ],
                [
                    'exists' => 'The :attribute doesn\'t belong to user:'.$this->loginUser->name
                ]
            );

            if($validator->fails()) {

               $error = implode(',', $validator->messages()->all());

                throw new Exception($error, 101);

            } else {

                $user_details = User::find($request->id);

                // No need to prevent the deafult card delete. We need to allow user to delete the all the cards

                // if($user_details->user_card_id == $user_card_id) {

                //     throw new Exception(tr('card_default_error'), 101);
                    
                // } else {

                    UserCard::where('id',$user_card_id)->delete();

                    if($user_details) {

                        if($user_details->payment_mode = CARD) {

                            // Check he added any other card

                            if($check_card = UserCard::where('user_id' , $request->id)->first()) {

                                $check_card->is_default =  DEFAULT_TRUE;

                                $user_details->user_card_id = $check_card->id;

                                $check_card->save();

                            } else { 

                                $user_details->payment_mode = COD;

                                $user_details->user_card_id = DEFAULT_FALSE;
                            
                            }
                       
                        }

                        // Check the deleting card and default card are same

                        if($user_details->user_card_id == $user_card_id) {

                            $user_details->user_card_id = DEFAULT_FALSE;

                            $user_details->save();
                        }
                        
                        $user_details->save();
                    
                    }

                    $response_array = ['success' => true , 'message' => Helper::success_message(107) , 'code' => 107];

                // }

            }

            DB::commit();
    
            return response()->json($response_array , 200);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }
    }

    /**
     * @method cards_default()
     *
     * @uses update the selected card as default
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param integer id
     * 
     * @return JSON Response
     */
    public function cards_default(Request $request) {

        Log::info("cards_default");

        try {

            DB::beginTransaction();

            $validator = Validator::make($request->all(),
                [
                    'user_card_id' => 'required|integer|exists:user_cards,id,user_id,'.$request->id,
                ],
                [
                    'exists' => 'The :attribute doesn\'t belong to user:'.$this->loginUser->name
                ]
            );

            if($validator->fails()) {

                $error = implode(',', $validator->messages()->all());

                throw new Exception($error, 101);
                   
            }

            $old_default_cards = UserCard::where('user_id' , $request->id)->where('is_default', DEFAULT_TRUE)->update(['is_default' => DEFAULT_FALSE]);

            $card = UserCard::where('id' , $request->user_card_id)->update(['is_default' => DEFAULT_TRUE]);

           //  $user_details = $this->loginUser;

            $user_details = User::find($request->id);

            $user_details->user_card_id = $request->user_card_id;

            $user_details->save();           

            DB::commit();

            return $this->sendResponse($message = Helper::success_message(108), $success_code = "108", $data = []);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }
    
    } 

    /**
     * @method payment_mode_default()
     *
     * @uses update the selected card as default
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param integer id
     * 
     * @return JSON Response
     */
    public function payment_mode_default(Request $request) {

        Log::info("payment_mode_default");

        try {

            DB::beginTransaction();

            $validator = Validator::make($request->all(),
                [
                    'payment_mode' => 'required',
                ]
            );

            if($validator->fails()) {

                $error = implode(',', $validator->messages()->all());

                throw new Exception($error, 101);
                   
            }

            $user_details = User::find($request->id);

            $user_details->payment_mode = $request->payment_mode ?: CARD;

            $user_details->save();           

            DB::commit();

            return $this->sendResponse($message = "Mode updated", $code = 200, $data = ['payment_mode' => $request->payment_mode]);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }
    
    } 

    /**
     * @method notification_settings()
     *
     * @uses To enable/disable notifications of email / push notification
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param - 
     *
     * @return JSON Response
     */
    public function notification_settings(Request $request) {

        try {

            DB::beginTransaction();

            $validator = Validator::make($request->all(),
                [
                    'status' => 'required|numeric',
                    'type' => 'required|in:'.EMAIL_NOTIFICATION.','.PUSH_NOTIFICATION
                ]
            );

            if($validator->fails()) {

                $error = implode(',', $validator->messages()->all());

                throw new Exception($error, 101);

            }
                
            $user_details = User::find($request->id);

            if($request->type == EMAIL_NOTIFICATION) {

                $user_details->email_notification_status = $request->status;

            }

            if($request->type == PUSH_NOTIFICATION) {

                $user_details->push_notification_status = $request->status;

            }

            $user_details->save();

            $message = $request->status ? Helper::success_message(206) : Helper::success_message(207);

            $data = ['id' => $user_details->id , 'token' => $user_details->token];

            $response_array = [
                'success' => true ,'message' => $message, 
                'email_notification_status' => (int) $user_details->email_notification_status,  // Don't remove int (used ios)
                'push_notification_status' => (int) $user_details->push_notification_status,    // Don't remove int (used ios)
                'data' => $data
            ];
                
            
            DB::commit();

            return response()->json($response_array , 200);

        } catch (Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }

    /**
     * @method configurations()
     *
     * @uses used to get the configurations for base products
     *
     * @created Vithya R Chandrasekar
     *
     * @updated - 
     *
     * @param - 
     *
     * @return JSON Response
     */
    public function configurations(Request $request) {

        try {

            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:users,id',
                'token' => 'required'
            ]);

            if($validator->fails()) {

                $error = implode(',',$validator->messages()->all());

                throw new Exception($error, 101);

            }

            // Update timezone details

            $user_details = User::find($request->id);

            $message = "";

            if($user_details && $request->timezone) {
                
                $user_details->timezone = $request->timezone ?: $user_details->timezone;

                $user_details->save();

                $message = tr('timezone_updated');
            }

            $config_data = $data = [];

            $payment_data['is_stripe'] = 1;

            $payment_data['stripe_publishable_key'] = Setting::get('stripe_publishable_key') ?: "";

            $payment_data['stripe_secret_key'] = Setting::get('stripe_secret_key') ?: "";

            $payment_data['stripe_secret_key'] = Setting::get('stripe_secret_key') ?: "";

            $data['payments'] = $payment_data;

            $data['urls']  = [];

            $url_data['base_url'] = envfile("APP_URL") ?: "";

            $url_data['chat_socket_url'] = Setting::get("chat_socket_url") ?: "";

            $url_data['refund_page_url'] = route('static_pages.view', ['type' => 'refund']);

            $url_data['cancellation_page_url'] = route('static_pages.view', ['type' => 'cancellation']);

            $data['urls'] = $url_data;

            $notification_data['FCM_SENDER_ID'] = "";

            $notification_data['FCM_SERVER_KEY'] = $notification_data['FCM_API_KEY'] = "";

            $notification_data['FCM_PROTOCOL'] = "";

            $data['notification'] = $notification_data;

            // Bookings 

            $bookings_data['min_time'] = 60;

            $data['bookings'] = $bookings_data;

            $data['site_name'] = Setting::get('site_name');

            $data['site_logo'] = Setting::get('site_logo');

            $data['currency'] = $this->currency;

            return $this->sendResponse($message, $code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());

        }
   
    }

    /**
     * @method home_first_section()
     *
     * @uses 
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param object $request id, host_id
     *
     * @return response of details
     */
    public function home_first_section(Request $request) {

        try {
 
            $user_details = User::find($request->id);

            $username = $user_details ? $user_details->name : "";

            $data = [];

            // Check the page type is see all section

            $see_all_page_types = [API_PAGE_TYPE_SEE_ALL, API_PAGE_TYPE_TOP_RATED, API_PAGE_TYPE_WISHLIST, API_PAGE_TYPE_RECENT_UPLOADED, API_PAGE_TYPE_SUGGESTIONS];

            if(in_array($request->api_page_type, $see_all_page_types)) {

                $see_all_data = HostRepo::see_all_section($request); 

                return $this->sendResponse($message = "", $code = "", $see_all_data);

            }

            // = = = = = = = = = =  Category Section START = = = = = = = = = 

            // Check the url type 

            $first_block_data = [];

            if(in_array($request->api_page_type, [API_PAGE_TYPE_CATEGORY, API_PAGE_TYPE_HOME])) {
                
                $first_block_data = HostHelper::first_block($request);

            }


            // = = = = = = = = = =  Category section END = = = = = = = = = 


            // = = = = = = = = = =  Location based section START = = = = = = = = = 

            $second_block_data = [];

            if(in_array($request->api_page_type, [API_PAGE_TYPE_CATEGORY, API_PAGE_TYPE_HOME, API_PAGE_TYPE_SUB_CATEGORY])) {

                $second_block_data = HostHelper::location_block($request);

            }

            // = = = = = = = = = =  Location based section END = = = = = = = = = 

            // = = = = = = = = = =  RECENT HOSTS Section START = = = = = = = = = 

            $recent_hosts = HostHelper::recently_uploaded_hosts($request);

            $recent_hosts_data['title'] = tr('URL_TYPE_RECENT_UPLOADED');

            $recent_hosts_data['description'] = "";

            $recent_hosts_data['api_page_type'] = API_PAGE_TYPE_RECENT_UPLOADED;

            $recent_hosts_data['api_page_type_id'] = $request->api_page_type_id ?: 0;

            $recent_hosts_data['is_see_all'] = YES;

            $recent_hosts_data['data'] = $recent_hosts;

            array_push($data , $recent_hosts_data);

            // = = = = = = = = = =  RECENT HOSTS Section END = = = = = = = = = 

            // = = = = = = = = = =  Top Rated hosts section start = = = = = = = = = 

            $top_rated_hosts = HostHelper::top_rated_hosts($request);

            $top_rated_hosts_data['title'] = tr('URL_TYPE_TOP_RATED');

            $top_rated_hosts_data['description'] = "";

            $top_rated_hosts_data['api_page_type'] = API_PAGE_TYPE_TOP_RATED;

            $top_rated_hosts_data['api_page_type_id'] = $request->api_page_type_id ?: 0;

            // $top_rated_hosts_data['url_type'] = URL_TYPE_TOP_RATED;

            // $top_rated_hosts_data['url_page_id'] = 0;

            $top_rated_hosts_data['is_see_all'] = YES;

            $top_rated_hosts_data['data'] = $top_rated_hosts;

            array_push($data , $top_rated_hosts_data);

            // = = = = = = = = = =  Top rated hosts section end = = = = = = = = = 

            // = = = = = = = = = =  Suggestions section start = = = = = = = = = 

            $suggestions = HostHelper::suggestions($request);

            $suggestions_data['title'] = tr('URL_TYPE_SUGGESTIONS');

            $suggestions_data['description'] = "";

            $suggestions_data['api_page_type'] = API_PAGE_TYPE_SUGGESTIONS;

            $suggestions_data['api_page_type_id'] = $request->api_page_type_id ?: 0;

            // $suggestions_data['url_type'] = URL_TYPE_SUGGESTIONS;

            // $suggestions_data['url_page_id'] = 0;

            $suggestions_data['is_see_all'] = YES;

            $suggestions_data['data'] = $suggestions;

            array_push($data , $suggestions_data);

            // = = = = = = = = = =  suggestions section end = = = = = = = = = 

            $response_array = ['success' => true , 'data' => $data, 'first_block' => $first_block_data, 'second_block' => $second_block_data]; 

            return response()->json($response_array , 200);

        } catch (Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());

        }

    }

    /**
     * @method home_second_section()
     *
     * @uses 
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param object $request id, host_id
     *
     * @return response of details
     */
    public function home_second_section(Request $request) {

        try {

            $user_details = User::find($request->id);

            $base_query = SubCategory::where('sub_categories.status' , APPROVED)
                            ->whereHas('hosts', function($q) {
                                $q->VerifedHostQuery();
                            })->withCount('hosts');

            if($request->api_page_type == API_PAGE_TYPE_CATEGORY) {

                $base_query = $base_query->where('category_id', $request->api_page_type_id);

            }

            $sub_categories = $base_query->skip($this->skip)->take($this->take)->get(); 

            $data = [];

            $section_types = [SECTION_TYPE_HORIZONTAL,SECTION_TYPE_VERTICAL,SECTION_TYPE_GRID];

            foreach ($sub_categories as $key => $sub_category_details) {

                $request->request->add(['sub_category_id' => $sub_category_details->id, 'skip' => 0]);

                $hosts = [];

                $hosts = HostHelper::sub_category_based_hosts($request);

                if(sizeof($hosts) > 0) {

                    $hosts_data['title'] = $sub_category_details->name;

                    $hosts_data['description'] = "";

                    $hosts_data['api_page_type'] = API_PAGE_TYPE_SUB_CATEGORY;

                    $hosts_data['api_page_type_id'] = $sub_category_details->id;

                    $hosts_data['is_see_all'] = YES;

                    $hosts_data['section_type'] = $section_types[array_rand($section_types, 1)];

                    $hosts_data['data'] = $hosts;

                    array_push($data , $hosts_data);

                }
            
            }

            return $this->sendResponse($message = "", $code = "", $data);

        } catch (Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }

    }

    /**
     *
     * @method see_all_section() 
     *
     * @uses used to get the first set of sections based on the page type
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param
     *
     * @return
     */

    public function see_all_section(Request $request) {

        Log::info("see_all_section".print_r($request->all(), true));

        try {

            switch ($request->api_page_type) {

                case API_PAGE_TYPE_RECENT_UPLOADED:
                    $hosts = HostHelper::recently_uploaded_hosts($request);
                    $title = tr('API_PAGE_TYPE_RECENT_UPLOADED');
                    $description = "";
                    break;

                case API_PAGE_TYPE_TOP_RATED:
                    $hosts = HostHelper::top_rated_hosts($request);
                    $title = tr('API_PAGE_TYPE_TOP_RATED');
                    $description = "";
                    break;

                case API_PAGE_TYPE_SUGGESTIONS:
                    $hosts = HostHelper::suggestions($request);
                    $title = tr('API_PAGE_TYPE_SUGGESTIONS');
                    $description = "";
                    break;

                case API_PAGE_TYPE_CATEGORY:
                    $request->request->add(['category_id' => $request->api_page_type_id]);
                    $hosts = HostHelper::category_based_hosts($request);
                    $title = tr('API_PAGE_TYPE_CATEGORY');
                    $description = "";
                    break;

                case API_PAGE_TYPE_SUB_CATEGORY:
                    $request->request->add(['sub_category_id' => $request->api_page_type_id]);
                    $hosts = HostHelper::sub_category_based_hosts($request);
                    $title = tr('API_PAGE_TYPE_SUB_CATEGORY');
                    $description = "";
                    break;

                default:
                    $hosts = HostHelper::suggestions($request);
                    $title = tr('API_PAGE_TYPE_SUGGESTIONS');
                    $description = "";
                    break;
            }

            $hosts_data['title'] = $title;

            $hosts_data['description'] = $description;

            $hosts_data['data'] = $hosts;

            $data = [];

            array_push($data, $hosts_data);

            return $this->sendResponse($message = "", $code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());

        }

    }
    /**
     * @method suggestions()
     *
     * @uses used get the hostings associated with selected category
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param object $request id, host_id
     *
     * @return response of details
     */

    public function suggestions(Request $request) {

        try {

            $validator = Validator::make($request->all(),[
                'category_id' => 'exists:categories,id,status,'.APPROVED,
                'sub_category_id' => 'exists:sub_categories,id,status,'.APPROVED,
                'host_id' => 'exists:hosts,id,status,'.APPROVED
            ]);

            if($validator->fails()) {

                $error = implode(",", $validator->messages()->all());
                
                throw new Exception($error, 101);
                
            }

            $base_query = Host::skip($this->skip)->take($this->take)->orderBy('hosts.updated_at' , 'desc');

            if($request->category_id) {
                
                $base_query = $base_query->where('hosts.category_id' , $request->category_id);
            
            }

            if($request->sub_category_id) {
                
                $base_query = $base_query->where('hosts.sub_category_id' , $request->sub_category_id);
            
            }

            if($request->host_id) {

                $base_query = $base_query->whereNotIn('hosts.id', [$request->host_id]);

                $host_details = Host::find($request->host_id);

                if($host_details) {
                    
                    $base_query = $base_query->where('hosts.category_id' , $host_details->category_id);

                }
            
            }

            $host_ids = $base_query->pluck('hosts.id');

            $hosts = [];

            if($host_ids) {
                $hosts = HostRepo::host_list_response($host_ids, $request->id);
            }

            return $this->sendResponse($message = "", $code = "", $hosts);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }
    /**
     * @method hostings_category_based()
     *
     * @uses used get the hostings associated with selected category
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param object $request id, host_id
     *
     * @return response of details
     */

    public function hostings_category_based(Request $request) {

        try {

            $validator = Validator::make($request->all(),[
                'category_id' => 'required|exists:categories,id,status,'.APPROVED
            ]);

            if($validator->fails()) {

                $error = implode(",", $validator->messages()->all());
                
                throw new Exception($error, 101);
                
            }

            $hosts = Host::where('hosts.category_id' , $request->category_id)
                            ->skip($this->skip)
                            ->take($this->take)
                            ->CommonResponse()
                            ->orderBy('hosts.updated_at' , 'desc')
                            ->get();

            return $this->sendResponse("", "", $hosts);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }

    /**
     * @method hostings_sub_category_based()
     *
     * @uses used get the hostings associated with selected sub category
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param object $request id, host_id
     *
     * @return response of details
     */

    public function hostings_sub_category_based(Request $request) {

        try {
            $validator = Validator::make($request->all(),[
                'sub_category_id' => 'required|exists:sub_categories,id'
            ]);

            if($validator->fails()) {

                $error = implode(",", $validator->messages()->all());
                
                throw new Exception($error, 101);
                
            }

            $hosts = Host::where('hosts.sub_category_id' , $request->sub_category_id)
                            ->skip($this->skip)
                            ->take($this->take)
                            ->CommonResponse()
                            ->orderBy('updated_at' , 'desc')
                            ->get();

            return $this->sendResponse($message = "", $code = "", $hosts);


        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }

    /**
     * @method hosts_view()
     *
     * @uses used to get the host details
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param object $request
     *
     * @return response of details
     */
    public function hosts_view(Request $request) {

        try {

            $host = Host::where('hosts.id', $request->host_id)
                                ->where('hosts.is_admin_verified', ADMIN_HOST_VERIFIED)
                                ->where('hosts.admin_status', ADMIN_HOST_APPROVED)
                                ->where('hosts.status', HOST_OWNER_PUBLISHED)
                                ->first();

            $host_details = HostDetails::where('host_id', $request->host_id)->first();

            if(!$host || !$host_details) {

                throw new Exception(Helper::error_message(200), 200);
                
            }

            // Create empty object

            $data = new \stdClass();

            /* # # # # # # # # # # BASIC DETAILS SECTION START # # # # # # # # # # */

            $basic_host_details = Host::where('hosts.id', $request->host_id)->SingleBaseResponse()->first();

            $basic_details = new \stdClass();

            $basic_details = $basic_host_details;

            $basic_details->share_link = Setting::get('frontend_url')."trip/".$request->host_id;

            $basic_details->min_days_text = $basic_details->min_days." nights min";

            $basic_details->max_days_text = $basic_details->max_days." nights max";
          
            // Actions
        
            $basic_details->wishlist_status = HostHelper::wishlist_status($host->id, $request->id);

            $basic_details->per_day_formatted = formatted_amount($basic_details->per_day);

            $basic_details->per_day_symbol = tr('list_per_day_symbol');

            // Gallery details 

            $host_galleries = HostGallery::where('host_id', $host->id)->select('picture', 'caption')->get();

            $basic_details->gallery = $host_galleries;

            // Section 4

            $section_4_data = $section_4 = [];

            $section_4_data['title'] = $host_details->max_guests." ".tr('guests');

            $section_4_data['picture'] = asset('sample/users.png');

            array_push($section_4, $section_4_data);

            $section_4_data = [];

            $section_4_data['title'] = $host_details->total_bedrooms." ".tr('bedrooms');

            $section_4_data['picture'] = asset('sample/bedroom.png');

            array_push($section_4, $section_4_data);

            $section_4_data = [];

            $section_4_data['title'] = $host_details->total_beds." ".tr('beds');

            $section_4_data['picture'] = asset('sample/bed.png');

            array_push($section_4, $section_4_data);

            $section_4_data = [];

            $section_4_data['title'] = $host_details->total_bathrooms." ".tr('bath');

            $section_4_data['picture'] = asset('sample/bath.png');

            array_push($section_4, $section_4_data);

            $basic_details->section_4 = $section_4;

            // Assign basic details to main data

            $data->basic_details = $basic_details;

            /* # # # # # # # # # # BASIC DETAILS SECTION END # # # # # # # # # # */

            

            /* @ @ @ @ @ @ @ @ @ @ PRICING SECTION START @ @ @ @ @ @ @ @ @ @ */

            $pricing_details = new \stdClass();

            $pricing_details->currency = $this->currency;

            $pricing_details->per_day_symbol = tr('list_per_day_symbol');

            $pricing_details->per_day = $host->per_day ?: 0.00;

            $pricing_details->per_day_formatted = formatted_amount($host->per_day);

            // $pricing_details->per_week = $host->per_week ?: 0.00;

            // $pricing_details->per_month = $host->per_month ?: 0.00;

            $pricing_details->service_fee = $host->service_fee ?: 0.00;

            $pricing_details->service_fee_formatted = formatted_amount($host->service_fee);

            $pricing_details->cleaning_fee = $host->cleaning_fee ?: 0.00;

            $pricing_details->cleaning_fee_formatted = formatted_amount($host->cleaning_fee);

            $pricing_details->tax_fee = $host->tax_fee ?: 0.00;

            $pricing_details->tax_fee_formatted = formatted_amount($host->tax_fee);

            $pricing_details->other_fee = $host->other_fee ?: 0.00;

            $pricing_details->other_fee_formatted = formatted_amount($host->other_fee);

            // Assign amenties to main data

            $data->pricing_details = $pricing_details;

            /* @ @ @ @ @ @ @ @ @ @ PRICING SECTION END @ @ @ @ @ @ @ @ @ @ */


            /* # # # # # # # # # # AMENTIES SECTION START # # # # # # # # # # */

            $amenties = HostQuestionAnswer::where('host_id', $request->host_id)->UserAmentiesResponse()->get();

            foreach ($amenties as $key => $amenity_details) {

                $answer_ids = explode(',', $amenity_details->common_question_answer_id);

                if($answer_ids) {

                    $answers = CommonQuestionAnswer::whereIn('id', $answer_ids)->select('common_answer as title', 'picture')->get()->toArray();

                    $amenity_details->answers_data = $answers;

                } else {
                    $amenties->forget($key); // to remove empty records from amenties object
                }
                
            }


            $amenties_data = new \stdClass();

            $amenties_data->title = "amenties";

            $amenties_data->data = $amenties;

            // Assign amenties to main data

            $data->amenties = $amenties_data;

            /* # # # # # # # # # # AMENTIES SECTION END # # # # # # # # # # */


            /* @ @ @ @ @ @ @ @ @ @ SLEEPTING ARRANGEMENTS SECTION START @ @ @ @ @ @ @ @ @ @ */


            $sleeping_data = [];

            $sleeping1['title'] = tr('bedrooms');

            $sleeping1['note'] = $host_details->total_bedrooms." ".tr('bedrooms');;

            $sleeping1['picture'] = asset('sample/bedroom.png');

            array_push($sleeping_data, $sleeping1);


            $sleeping2['title'] = tr('beds'); 

            $sleeping2['note'] = $host_details->total_beds." ".tr('beds');

            $sleeping2['picture'] = asset('sample/bed.png');

            array_push($sleeping_data, $sleeping2);


            $sleeping3['title'] = tr('bathrooms');

            $sleeping3['note'] = $host_details->total_bathrooms." ".tr('bathrooms');

            $sleeping3['picture'] = asset('sample/bath.png');

            array_push($sleeping_data, $sleeping3);
            

            $sleeping_arrangement_data = new \stdClass();

            $sleeping_arrangement_data->title = tr('sleeping_arrangements');

            $sleeping_arrangement_data->data = $sleeping_data;

            // Assign amenties to main data

            $data->arrangements = $sleeping_arrangement_data;


            /* @ @ @ @ @ @ @ @ @ @ SLEEPTING ARRANGEMENTS SECTION END @ @ @ @ @ @ @ @ @ @ */

            // Host provider details

            $provider_details = Provider::where('id', $host->provider_id)->FullResponse()->first();

            $provider_details->total_reviews = BookingUserReview::where('provider_id', $host->provider_id)->count() ?? 0;

            $provider_details->overall_ratings = BookingUserReview::where('provider_id', $host->provider_id)->avg('ratings') ?? 0;

            $data->provider_details = $provider_details;

            $data->questions =[];

            // Rules 

            $policies_data = HostHelper::host_policies($request->host_id);

            $policies = new \stdClass();

            $policies->title = tr('policies_rules');

            $policies->data = $policies_data;


            // Assign amenties to main data

            $data->policies = $policies;


            // Other Questions

            return $this->sendResponse($message = "", $code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }

    /**
     * @method hosts_availability()
     *
     * @uses used to get the host details
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param object $request
     *
     * @return response of details
     */
    public function hosts_availability(Request $request) {

        try {

            $request->request->add(['loops' => (int) $request->loops]);

            $validator = Validator::make($request->all(), [
                            'host_id' => 'required|exists:hosts,id',
                            'month' => 'required',
                            'year' => 'required',
                            'loops' => 'max:2|min:1',
                        ],[
                            'required' => Helper::error_message(202),
                            'exists.host_id' => Helper::error_message(200),
                        ]
                    );

            if($validator->fails()) {

                $error = implode(',', $validator->messages()->all());

                throw new Exception($error, 101);
                
            }

            $host = Host::where('hosts.id', $request->host_id)
                                ->where('hosts.is_admin_verified', ADMIN_HOST_VERIFIED)
                                ->where('hosts.admin_status', ADMIN_HOST_APPROVED)
                                ->where('hosts.status', HOST_OWNER_PUBLISHED)
                                ->first();

            $host_details = HostDetails::where('host_id', $request->host_id)->first();

            if(!$host || !$host_details) {

                throw new Exception(Helper::error_message(200), 200);
                
            }

            $host_availabilities = HostAvailability::where('host_id', $request->host_id)->where('status', AVAILABLE)->get();

            $currency = $this->currency;

            $data = [];

            $data_ranges = HostHelper::generate_date_range($request->year, $request->month, "+1 day", "Y-m-d", $request->loops ?: 2);

            foreach ($data_ranges as $key => $data_range_details) {

                foreach ($data_range_details->dates as $check => $date_details) {

                    $availability_data = new \stdClass;

                    $check_host_availablity = HostAvailability::where('host_id', $request->host_id)->where('available_date', $date_details)->first();

                    $availability_data->date = $date_details;

                    $availability_data->is_available = $check_host_availablity ? $check_host_availablity->status: AVAILABLE;

                    $availability_data->is_blocked_booking = $check_host_availablity ? $check_host_availablity->is_blocked_booking : NO;

                    // The user can't book today date

                    if(strtotime($date_details) <= strtotime(date('Y-m-d'))) {
                        
                        $availability_data->is_available = NOTAVAILABLE;

                        $availability_data->is_blocked_booking = YES;

                    }

                    $availability_data->min_dates = $host_details->min_guests ?: 0;

                    $availability_data->max_dates = $host_details->max_guests ?: 0;

                    $price_details = new \stdClass;

                    $price_details->currency = $currency;

                    $price_details->price = $host->base_price;

                    $price_details->price_formatted = formatted_amount($host->base_price);

                    $price_details->per_day = $host->per_day;

                    $price_details->per_day_formatted = formatted_amount($host->per_day);

                    $availability_data->pricings = $price_details;

                    $now_data[] = $availability_data;

                }

                $first_month_data['title'] = $first_month_data['month'] = $data_range_details->month;

                $first_month_data['total_days'] = $data_range_details->total_days;

                $first_month_data['from_month'] = $request->month;

                // Todate find

                $to_date = Carbon::createFromDate($request->year, $request->month, 01)->addMonth($request->loops - 1)->day(01);

                $to_year = $to_date->year;

                $to_month = $to_date->month;

                $first_month_data['to_month'] = $to_month;

                $first_month_data['availability_data'] = $now_data;

                $data[] = $first_month_data;

            }

            return $this->sendResponse($message = "", $code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }

    /**
     * @method providers_view()
     *
     * @uses used to get the provider details
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param object $request
     *
     * @return response of details
     */
    public function providers_view(Request $request) {

        try {

            $provider_details = Provider::where('providers.status', PROVIDER_APPROVED)
                                    ->where('providers.is_verified', PROVIDER_EMAIL_VERIFIED)
                                    ->where('providers.id', $request->provider_id)
                                    ->FullResponse()
                                    ->first();

            if(!$provider_details) {

                throw new Exception(Helper::error_message(201), 201);
                
            }

            $provider_details->total_reviews = BookingUserReview::where('provider_id', $request->provider_id)->count();

            $provider_details->overall_ratings = BookingUserReview::where('provider_id', $request->provider_id)->avg('ratings') ?: 0;

            $host_ids = Host::VerifedHostQuery()->where('hosts.provider_id', $request->provider_id)->pluck('hosts.id')->toArray();

            $hosts = HostRepo::host_list_response($host_ids, $request->id);

            $provider_details->hosts = $hosts;

            $provider_details->total_hosts = count($hosts);

            return $this->sendResponse($message = "", $code = "", $provider_details);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }

    /**
     * @method reviews_index()
     *
     * @uses used to get the reviews based review_type = provider | Host
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param object $request
     *
     * @return response of details
     */
    public function reviews_index(Request $request) {

        try {

            $validator = Validator::make($request->all(), 
                        [
                            'host_id' => 'exists:hosts,id',
                            'provider_id' => 'exists:providers,id',
                            
                        ],
                        [
                            'required' => Helper::error_message(202),
                            'exists.host_id' => Helper::error_message(200),
                            'exists.provider_id' => Helper::error_message(201)
                        ]
                    );

            if($validator->fails()) {

                $error = implode(',', $validator->messages()->all());

                throw new Exception($error, 101);
                
            }

            $base_query = BookingUserReview::leftJoin('users', 'users.id', '=', 'booking_user_reviews.user_id')
                                ->leftJoin('providers', 'providers.id', '=', 'booking_user_reviews.provider_id')
                                ->leftJoin('hosts', 'hosts.id', '=', 'booking_user_reviews.host_id')
                                ->select('booking_user_reviews.id as booking_user_review_id', 
                                        'hosts.host_name as host_name','user_id','users.name as user_name', 
                                        'users.picture as user_picture', 'providers.name as provider_name',
                                        'providers.id as provider_id', 'providers.picture as provider_picture',
                                        'ratings', 'review', 'booking_user_reviews.created_at', 'booking_user_reviews.updated_at');

            if($request->host_id) {

                $basic_query = $base_query->where('booking_user_reviews.host_id', $request->host_id);

            }

            if($request->provider_id) {

                $basic_query = $base_query->where('booking_user_reviews.provider_id', $request->provider_id);

            }

            $reviews = $base_query->skip($this->skip)->take($this->take)->get();

            foreach ($reviews as $key => $review_details) {

                $review_details->updated = common_date($review_details->updated_at);
                
                $review_details->ratings = floatval(number_format($review_details->ratings + 0.01, 2));
            }

            return $this->sendResponse($message = "", $code = "", $reviews);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }

    /**
     * @method other_users_view()
     *
     * @uses used to get the provider details
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param object $request
     *
     * @return response of details
     */
    public function other_users_view(Request $request) {

        try {

            $user_details = User::where('users.status', USER_APPROVED)->where('users.is_verified', USER_EMAIL_VERIFIED)
                                    ->where('users.id', $request->user_id)
                                    ->OtherCommonResponse()
                                    ->first();

            if(!$user_details) {

                throw new Exception(Helper::error_message(201), 201);
                
            }

            $user_reviews = BookingProviderReview::where('user_id', $request->user_id)->select('*', 'id as booking_user_review_id')->get();

            foreach ($user_reviews as $key => $value) {
                
            }

            $user_details->total_reviews = count($user_reviews);

            $user_details->reviews = $user_reviews;

            return $this->sendResponse($message = "", $code = "", $user_details);


        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }

    /**
     * @method bookings_steps_info()
     *
     * @uses used to get the search result based on the search key
     *
     * @created Vidhya R
     *
     * @updated Vidhya R
     *
     * @param
     *
     * @return json response 
     */

    public function bookings_steps_info(Request $request) {

        try {

            $validator = Validator::make($request->all(),[
                'host_id' => 'required|integer|exists:hosts,id',
                'booking_id' => 'integer|exists:bookings,id',
                'step' => 'required|integer',
                'rebooking_id' => 'nullable|exists:bookings,id'
            ]);

            if($validator->fails()) {

                $error = implode(',', $validator->messages()->all());

                throw new Exception($error, 101);

            }

            $host = Host::find($request->host_id);

            $host_details = HostDetails::where('host_id', $request->host_id)->first();

            $user_details = $this->loginUser;

            if(!$host || !$host_details) {
                
                throw new Exception(Helper::error_message(200), 200);

            }

            $booking_details = Booking::where('id', $request->booking_id)->where('host_id', $request->host_id)->first();

            if($request->step != 1) {

                if(!$booking_details) {

                    throw new Exception(Helper::error_message(206), 206);

                }
            }

            $rebooking_details = new Booking;

            if($request->rebooking_id) {

                $rebooking_details = Booking::find($request->rebooking_id);
            }

            $total_steps = 6; $data = new \stdClass;

            /* * * * * *  Step 1 * * * * * */

            $step1_details = new \stdClass;

            $step1_details->title = tr('api_booking_step1_title');

            $step1_details->description = "";

            $step1_details->step = 1;

            $step1_details->should_display_the_step = YES;

            // Default values

            if($booking_details) {

                $checkin = common_date($booking_details->checkin, $this->timezone, 'd M Y');

                $checkout = common_date($booking_details->checkout, $this->timezone, 'd M Y');

                $adults = $booking_details->adults ?: 0;

                $children = $booking_details->children ?: 0;

                $infants = $booking_details->infants ?: 0;

                $total_guests = $booking_details->total_guests ?: 0;

            } else {

                $adults = $total_guests = $rebooking_details ? $rebooking_details->total_guests : ($host_details->total_guests ?: 1);

                $children = $rebooking_details ? $rebooking_details->children : 0;

                $infants = $rebooking_details ? $rebooking_details->infants : 0;

                $min_days = $host->min_days ?: 1;

                $max_days = $host->max_days ?: 1;

                $checkin = date('Y-m-d');

                $checkout = new Carbon(Carbon::parse($checkin)->addDay($max_days)->format('Y-m-d'));

                $checkout = $checkout->toDateString();

            }

            // DATES

            $step1_data['checkin'] = $checkin;

            $step1_data['checkout'] = $checkout;

            // GUESTS

            $step1_data['adults'] = $adults ?: 0;

            $step1_data['children'] = $children?: 0;

            $step1_data['infants'] = $infants ?: 0;

            $step1_data['total_guests'] = $adults + $children;

            $step1_details->data = $step1_data;

            // Update main data

            $data->step1 = $step1_details;

            /* * * * * *  Step 1 * * * * * */


            /* * * * * *  Step 2 * * * * * */

            $step2_details = new \stdClass;

            $step2_details->title = "Review ".$host_details->host_name." Rules";

            $step2_details->step = 2;

            $step2_details->description = "";

            $step2_details->should_display_the_step = YES;

            $step2_dates = $step2_data = [];

            // Dates

            $step2_data['checkin_date'] = $step1_data['checkin'];

            $step2_data['checkin_time'] = $host->checkin;

            $step2_data['checkout_date'] = $step1_data['checkout'];

            $step2_data['checkout_time'] = $host->checkout;

            // Rules

            $policies_data = HostHelper::host_policies($request->host_id);

            $step2_data['rules'] = $policies_data;

            // $rules_data['title'] = $host->host_name."'s rules";

            // $rules_data['description'] = "You will be shared ".$host->host_name."'s house. They have a few guidelines to help your stay";

            $step2_details->data = $step2_data;

            $data->step2 = $step2_details;

            /* * * * * *  Step 2 * * * * * */

            /* * * * * *  Step 3 * * * * * */

            $step3_details = new \stdClass;

            $step3_details->title = "Tell Your Host about your trip";

            $step3_details->step = 3;

            $step3_details->should_display_the_step = YES;

            $step3_details->description = "Help your host prepare for your stay by answering the question";
            
            $step3_details->provider_description = "Hi there..!! Welcome to ".$host->host_name;

            $step_3_data['description'] = $rebooking_details ? $rebooking_details->description : ($booking_details ? $booking_details->description : "");

            $step3_details->data = $step_3_data;

            $data->step3 = $step3_details;

            /* * * * * *  Step 3 * * * * * */

            /* * * * * *  Step 4 * * * * * */

            $step5_details = new \stdClass;

            $step5_details->title = "Confirm your phone number";

            $step5_details->step = 4;

            $step5_details->description = "This is so your can contact your during your trip, and let you know how to reach you.";

            $step_5_data['mobile'] = $user_details->mobile;

            $step5_details->data = $step_5_data;

            $step5_details->should_display_the_step = YES;

            $data->step4 = $step5_details;

            /* * * * * *  Step 4 * * * * * */

             /* * * * * *  Step 5 * * * * * */

            $step6_details = new \stdClass;

            $step6_details->title = "Review and pay";

            $step6_details->step = 5;

            $step6_details->should_display_the_step = YES;

            $step6_details->description = "";

            $step6_details->data = [];

            $data->step5 = $step6_details;

            /* * * * * *  Step 5 * * * * * */

            $main_data['steps'] = $data;

            $main_data['total_steps'] = 5;

            $main_data['active_steps'] = 5;

            return $this->sendResponse($message = "", $code = "", 
            $main_data);

        } catch(Exception  $e) {
            
            Log::info("Booking steps Error".print_r($e->getMessage(), true));

            return $this->sendError($e->getMessage(), $e->getCode());

        }

    }

    /**
     * @method bookings_create()
     *
     * @uses used to get the list of bookings
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param object $request
     *
     * @return response of details
     */

    public function bookings_create(Request $request) {

        try {
            
            DB::beginTransaction();

            $today = date('Y-m-d');

            // Formate checkin and checkout dates
            
            $checkin = $request->checkout ? common_date($request->checkin, $this->timezone = "" ,'Y-m-d H:i:s') : "";

            $checkout = $request->checkout ? common_date($request->checkout, $this->timezone = "" ,'Y-m-d H:i:s') : "";

            $request->request->add(['checkin' => $checkin, 'checkout' => $checkout]);

            $validator = Validator::make($request->all(), [

                'host_id' => 'required|exists:hosts,id',
                'checkin' => 'required|date|after:'.$today.'|bail',
                'checkout' => 'required_if:checkin,|date|after:checkin|bail',
                'description' => '',
                'total_guests' => 'integer',
                'adults' => 'integer|min:1',
                'children' => 'integer|min:0',
                'infants' => 'integer|min:0',
                'payment_mode' => 'required'
            ]);

            if($validator->fails()) {

                $error = implode(',', $validator->messages()->all());

                throw new Exception($error, 101);
            }

            $user_details = $this->loginUser;

            $host = Host::where('hosts.id', $request->host_id)->VerifedHostQuery()->first();

            $host_details = HostDetails::where('host_id', $request->host_id)->first();

            if(!$host || !$host_details) {

                throw new Exception(Helper::error_message(200), 200);
                
            }

            if($request->booking_id) {

                $booking_details = Booking::find($request->booking_id);

                // If the booking is not exists send error message

                if(!$booking_details) {

                    throw new Exception(Helper::error_message(206), 206); 
                
                }

                // Check the status of booking

                if($booking_details->status == BOOKING_DONE_BY_USER) {

                    throw new Exception(Helper::error_message(226), 226);
                    
                }

            } else {

                $booking_details = new Booking;

                $booking_details->host_id = $request->host_id;

                $booking_details->user_id = $request->id;

                $booking_details->provider_id = $host->provider_id;

                $booking_details->per_day = $host->per_day;

                $booking_details->currency = $this->currency;

                $booking_details->payment_mode = $request->payment_mode ?: "";

                $booking_details->save();

            }

            // Check the payment mode and handle default card validation

            if($request->payment_mode == CARD) {

                // Check the user have default card

                $check_default_card = UserCard::where('user_id' , $request->id)->where('is_default', YES)->count();

                if($check_default_card == 0) {

                    throw new Exception(Helper::error_message(112), 112);
                    
                }

            }

            // $booking_details->host_checkin = $host->checkin ?: "";

            // $booking_details->host_checkout = $host->checkout ?: "";

            $booking_details->description = $request->description ?: $booking_details->description;

            $booking_details->payment_mode = $request->payment_mode ?: "";

            // Check the host available on the selected dates

            if($request->checkin && $request->checkout) {

                $check_host_availablity = HostHelper::check_host_availablity($request->checkin, $request->checkout, $request->host_id);

                if($check_host_availablity == NO) {

                    // throw new Exception(Helper::error_message(210), 210);  
                }

                $booking_details->checkin = $request->checkin ? date('Y-m-d H:i:s', strtotime($request->checkin)) : $booking_details->checkin;

                $booking_details->checkout = $request->checkout ? date('Y-m-d H:i:s', strtotime($request->checkout)) : $booking_details->checkout;

                $booking_details->total_days = total_days($request->checkin, $request->checkout);

            }


            // Validate guests details

            $adults = $request->has('adults') ? $request->adults : ($booking_details->adults ?: $host_details->min_guests);

            $children = $request->has('children') ? $request->children : ($booking_details->children ?: 0);

            $total_guests = $adults + $children;

            // Check the total guests is equal or greater than the hosts min guests

            if($total_guests < $host_details->min_guests) {

                // throw new Exception(Helper::error_message(211, $host_details->min_guests), 211);
                
            }

            if($total_guests > $host_details->max_guests) {

                // throw new Exception(Helper::error_message(212, $host_details->max_guests), 212);
                
            }

            $booking_details->total_guests = $total_guests ?: $host_details->min_guests;

            $booking_details->adults = $adults ?: $host_details->min_guests;

            $booking_details->children = $children ?: $booking_details->children;

            $booking_details->infants = $request->infants ?: $booking_details->infants;

            $booking_details->save();

            // Check the booking status and update the inventory

            BookingRepo::check_booking_status($booking_details, $request);

            $payment_id = "";

            // After checked the status, check all the status and do the payment

            $is_booking_completed = NO; $booking_payment_details = [];

            if($booking_details->availability_step && $booking_details->basic_details_step && $booking_details->review_payment_step) {

                // Update the pricings

                $per_day_price = $booking_details->per_day;

                $time_price = $per_day_price * $booking_details->total_days;

                // Additional guest prices

                $additional_guests = $total_guests > $host_details->min_guests ? $total_guests-$host_details->min_guests : 0;

                $per_day_all_additional_guest_price = $host->per_guest_price * $additional_guests ?? 0;

                $total_additional_guest_price = $per_day_all_additional_guest_price * $booking_details->total_days;

                $booking_details->total_additional_guest_price = $total_additional_guest_price;

                // Additional guest prices end 

                $tax_price = 0.00;

                $sub_total = $time_price + $host->cleaning_fee + $total_additional_guest_price;

                $booking_details->total = $sub_total + $tax_price;

                $booking_details->save();

                // Update the payment details

                $booking_payment_details = BookingPayment::where('booking_id', $booking_details->id)->first();

                if(!$booking_payment_details) {

                    $booking_payment_details = new BookingPayment;

                }

                $booking_payment_details->booking_id = $booking_details->id;

                $booking_payment_details->user_id = $booking_details->user_id;

                $booking_payment_details->provider_id = $booking_details->provider_id;

                $booking_payment_details->host_id = $booking_details->host_id;

                $booking_payment_details->payment_id = rand();

                $booking_payment_details->payment_mode = $request->payment_mode ?: $user_details->payment_mode;

                $booking_payment_details->currency = $this->currency;

                $booking_payment_details->total_time = $booking_details->total_days ?: 1;

                $booking_payment_details->per_day = $host->per_day ?: "0.00";

                // Additional guest prices

                $booking_payment_details->per_guest_price = $host->per_guest_price ?: "0.00";

                $booking_payment_details->total_additional_guest_price = $total_additional_guest_price ?? 0.00;

                $booking_payment_details->time_price = $time_price ?: "0.00";

                $booking_payment_details->cleaning_fee = $host->cleaning_fee ?: "0.00";

                $booking_payment_details->sub_total = $sub_total ?: "0.00";

                $booking_payment_details->tax_price = $tax_price ?: "0.00";

                $booking_payment_details->actual_total = $booking_payment_details->total = $booking_details->total ?: "0.00";

                $booking_payment_details->status = PAYMENT_INITIATED;

                $booking_payment_details->save();


                $payment_response = BookingRepo::bookings_payment_by_stripe($request, $booking_details, $booking_payment_details)->getData();

                if($payment_response->success == false) {

                    DB::rollback();

                    $response_array = json_decode(json_encode($payment_response), true);

                    return response()->json($response_array, 200);
                }

                $booking_details->status = BOOKING_DONE_BY_USER;

                $booking_details->save();

                // Update the host availability
            
                BookingRepo::booking_update_host_availability($booking_details);

                $is_booking_completed = YES;

                $provider_details = Provider::where('id', $booking_details->provider_id)->VerifiedProvider()->first();

                if (Setting::get('is_push_notification') == YES && $provider_details) {
                
                    $title = $content = Helper::push_message(603);

                    $this->dispatch(new BellNotificationJob($booking_details, BELL_NOTIFICATION_TYPE_BOOKING_DONE_BY_USER, $content,BELL_NOTIFICATION_RECEIVER_TYPE_PROVIDER, $booking_details->id, $booking_details->user_id, $booking_details->provider_id));

                    if($provider_details->push_notification_status == YES && ($provider_details->device_token != '')) {

                        $push_data = ['booking_id' => $booking_details->id, 'type' => PUSH_NOTIFICATION_REDIRECT_BOOKING_VIEW];
                       
                        PushRepo::push_notification($provider_details->device_token, $title, $content, $push_data, $provider_details->device_type);
                    }
                }

                if(Setting::get('is_email_notification') == YES && $provider_details) {

                    $email_data['subject'] = Setting::get('site_name').'-'.tr('bookings_created_for_the_host');

                    $email_data['page'] = "emails.users.bookings.confirmation";

                    $email_data['email'] = $provider_details->email;

                    $data['booking_details'] = $booking_details->toArray();
                    
                    $data['host_details'] = $booking_details->hostDetails->toArray() ?? [];

                    $email_data['data'] = $data;

                    $this->dispatch(new SendEmailJob($email_data));
                                        
                }

            } else {

                if($request->step == 5) {

                    // Check the all steps completed

                    $error = $booking_details->availability_step == NO ? tr('booking_create_availability_step_details_missing'): "";

                    $error .= $booking_details->basic_details_step == NO ? tr('booking_create_basic_details_step_details_missing') : "";

                    $error .= $booking_details->review_payment_step == NO ? tr('booking_create_review_payment_step_details_missing') : "";

                    DB::commit();

                    return $this->sendError($error, 101);

                }
            }

            DB::commit();

            $booking_details = Booking::where('bookings.id', $booking_details->id)->CommonResponse()->first();

            $booking_details->total_formatted = formatted_amount($booking_details->total);

            $booking_details->payment_id = $booking_payment_details ? $booking_payment_details->payment_id : "-";

            $booking_details->is_booking_completed = $is_booking_completed;

            $booking_details->payment_mode = $request->payment_mode ?: CARD;

            return $this->sendResponse($message = Helper::success_message(2013), $success_code = 2013, $booking_details);

        } catch(Exception $e) {

            DB::rollback();

            Log::info("Booking Create Error".print_r($e->getMessage(), true));

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }

    /**
     * @method bookings_view()
     *
     * @uses used to get the list of bookings
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param object $request
     *
     * @return response of details
     */

    public function bookings_view(Request $request) {

        try {

            $validator = Validator::make($request->all(), [
                'booking_id' => 'required|exists:bookings,id,user_id,'.$request->id,

            ]);

            if($validator->fails()) {

                $error = implode(',', $validator->messages()->all());

                throw new Exception($error, 101);
            }

            $booking_details = Booking::where('user_id', $request->id)->where('id', $request->booking_id)->first();

            if(!$booking_details) {

                throw new Exception(Helper::error_message(206), 206); 
            }

            $host_details = Host::where('hosts.id', $booking_details->host_id)->VerifedHostQuery()->first();

            if(!$host_details) {

                throw new Exception(Helper::error_message(200), 200);
                
            }

            $user_details = $this->loginUser;

            $data = new \stdClass;

            $data->booking_id = $booking_details->id;

            $data->booking_unique_id = $booking_details->unique_id;

            $data->booking_description = $booking_details->description && $booking_details->description != "null" ? $booking_details->description : "";

            $data->host_id = $booking_details->host_id;

            $data->host_name = $host_details->host_name;

            $data->host_unique_id = $host_details->unique_id;

            $data->host_type = $host_details->host_type;

            $data->picture = $host_details->picture;

            $data->wishlist_status = HostHelper::wishlist_status($booking_details->host_id, $request->id);

            $data->share_link = url('/');

            $data->location = $host_details->serviceLocationDetails->name ?? "";

            $data->latitude = $host_details->latitude ?: "";

            $data->longitude = $host_details->longitude ?: "";

            $data->full_address = $host_details->full_address ?: "";

            $data->host_description = $host_details->description;

            $data->security_code = $host_details->security_code ?: "";

            $data->access_method = $host_details->access_method ?: "";

            $data->access_note = $host_details->access_note ?: "";

            $data->host_owner_type = $host_details->host_owner_type ?: "";

            $data->total_days = $booking_details->total_days;

            $data->checkin_time = common_date($booking_details->checkin, $this->timezone, "H:i A");

            $data->checkout_time = common_date($booking_details->checkout, $this->timezone, "H:i A");

            $data->checkin = common_date($booking_details->checkin, $this->timezone, "d M Y");

            $data->checkout = common_date($booking_details->checkout, $this->timezone, "d M Y");

            $data->duration = $booking_details->duration;

            $data->overall_ratings = $host_details->overall_ratings ?: 0;

            $data->total_ratings = $host_details->total_ratings ?: 0;

            $data->currency = $booking_details->currency;

            $data->total = $booking_details->total;

            $data->total_formatted = formatted_amount($booking_details->total);

            $host_galleries = HostGallery::where('host_id', $host_details->id)->select('picture', 'caption')->get();

            $data->gallery = $host_galleries;

            $provider_details = Provider::where('id', $host_details->provider_id)
                                        ->select('id as provider_id', 
                                            'username as provider_name', 'email', 'picture', 'mobile', 'description','created_at')
                                        ->first();

            $data->provider_details = $provider_details ?? [];

            $booking_payment_details = $booking_details->bookingPayments;


            $pricing_details = new \stdClass();

            $pricing_details->currency = $this->currency;


            $pricing_details->payment_id = $booking_payment_details->payment_id ?: "";

            $pricing_details->payment_mode = $booking_payment_details->payment_mode ?: "CARD";

            $pricing_details->per_hour = $host_details->per_hour ?: 0.00;

            $pricing_details->per_hour_formatted = formatted_amount($host_details->per_hour);


            $pricing_details->per_day = $host_details->per_day ?: 0.00;

            $pricing_details->per_day_formatted = formatted_amount($host_details->per_day);

            $pricing_details->per_week = $host_details->per_week ?: 0.00;

            $pricing_details->per_week_formatted = formatted_amount($host_details->per_week);


            $pricing_details->per_month = $host_details->per_month ?: 0.00;

            $pricing_details->per_month_formatted = formatted_amount($host_details->per_month);


            $pricing_details->service_fee = $host_details->service_fee ?: 0.00;

            $pricing_details->service_fee_formatted = formatted_amount($host_details->service_fee);


            $pricing_details->tax_fee = $host_details->tax_fee ?: 0.00;

            $pricing_details->tax_fee_formatted = formatted_amount($host_details->tax_fee);


            $pricing_details->other_fee = $host_details->other_fee ?: 0.00;

            $pricing_details->other_fee_formatted = formatted_amount($host_details->other_fee);


            $pricing_details->paid_amount = $booking_payment_details->paid_amount ?: 0.00;

            $pricing_details->paid_amount_formatted = formatted_amount($booking_payment_details->paid_amount ?: 0.00);

            $pricing_details->paid_date = common_date($booking_payment_details->paid_date ?: date('Y-m-d'));

            $data->pricing_details = $pricing_details;

            $data->status_text = booking_status($booking_details->status);

            $data->buttons = booking_btn_status($booking_details->status, $booking_details->id);

            $data->vehicle_details = UserVehicle::CommonResponse()->where('user_vehicles.id', $booking_details->user_vehicle_id)->first();

            $data->cancelled_date = common_date($booking_payment_details->cancelled_date ?: date('Y-m-d'));

            $data->cancelled_reason = $booking_details->cancelled_reason;

            $reviews = BookingUserReview::where('booking_id', $request->booking_id)->select('review', 'ratings', 'id as booking_review_id')->first();

            $data->reviews = $reviews ?: [];

            return $this->sendResponse($message = "", $code = "", $data);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }

    /**
     * @method bookings_cancel()
     *
     * @uses used to get the list of bookings
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param object $request
     *
     * @return response of details
     */
    public function bookings_cancel(Request $request) {

        try {


            $validator = Validator::make($request->all(), [

                'booking_id' => 'required|exists:bookings,id,user_id,'.$request->id
            ]);

            if($validator->fails()){

                $error = implode(',', $validator->messages()->all());

                throw new Exception($error , 101);

            }
            
            $booking_details = Booking::where('bookings.id', $request->booking_id)->where('user_id', $request->id)->first();

            if(!$booking_details) {

                throw new Exception(Helper::error_message(206), 206);
            }

            // check the required status to cancel the booking

            $cancelled_status = [BOOKING_CANCELLED_BY_USER, BOOKING_CANCELLED_BY_PROVIDER];

            if(in_array($booking_details->status, $cancelled_status)) {

                throw new Exception(Helper::error_message(209), 209);
                
            }

            // After checkin the user can't cancel the booking 

            if($booking_details->status == BOOKING_CHECKIN) {
                
                throw new Exception(Helper::error_message(217), 217);

            }

            DB::beginTransaction();

            $booking_details->status = BOOKING_CANCELLED_BY_USER;

            $booking_details->cancelled_reason = $request->cancelled_reason ?: "";

            $booking_details->cancelled_date = date('Y-m-d H:i:s');

            if($booking_details->save()) {

                // Reduce the provider amount from provider redeems
                BookingRepo::revert_provider_redeems($booking_details);

                // Add refund amount to the user
                BookingRepo::add_user_refund($booking_details);

                BookingRepo::revert_host_availability($booking_details);

                DB::commit();

                $provider_details = Provider::where('id', $booking_details->provider_id)->VerifiedProvider()->first();

                // Push Notification

                if (Setting::get('is_push_notification') == YES && $provider_details) {

                    $title = $content = Helper::push_message(604);

                    $this->dispatch(new BellNotificationJob($booking_details, BELL_NOTIFICATION_TYPE_BOOKING_CANCELLED_BY_USER,$content,BELL_NOTIFICATION_RECEIVER_TYPE_PROVIDER,$booking_details->id,$booking_details->user_id,$booking_details->provider_id));

                    if($provider_details->push_notification_status == YES && ($provider_details->device_token != '')) {

                        $push_data = ['type' => PUSH_NOTIFICATION_REDIRECT_BOOKINGS, 'booking_id' => $booking_details->id];
                       
                        PushRepo::push_notification($provider_details->device_token, $title, $content, $push_data, $provider_details->device_type);
                    }
                }
                
                if(Setting::get('is_email_notification') == YES && $provider_details) {

                    $email_data['subject'] = Setting::get('site_name').'-'.tr('user_cancel_booking_subject', $booking_details->unique_id);

                    $email_data['page'] = "emails.providers.bookings.cancel";

                    $email_data['email'] = $provider_details->email;

                    $data['booking_details'] = $booking_details->toArray();
                    
                    $data['host_details'] = $booking_details->hostDetails->toArray() ?? [];

                    $email_data['data'] = $data;

                    $this->dispatch(new SendEmailJob($email_data));
                                        
                }

                $data = ['booking_id' => $booking_details->id];

                return $this->sendResponse(Helper::success_message(213), $code = 213, $data);

            } else {
                
                throw new Exception(Helper::error_message(208), 208);

            }

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }

    /**
     * @method bookings_rating_report()
     *
     * @uses used to get the list of bookings
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param object $request
     *
     * @return response of details
     */
    public function bookings_rating_report(Request $request) {

        try {

            $validator = Validator::make($request->all(), [
                'booking_id' => 'required|exists:bookings,id', 
                'ratings' => 'required|min:1',
                'review' => 'required'
            ]);

            if($validator->fails()) {

                $error = implode(",", $validator->messages()->all());
                
                throw new Exception($error, 101);
            }

            DB::beginTransaction();

            // Check the booking is exists and belongs to the logged in user

            $booking_details = Booking::where('user_id', $request->id)->where('id', $request->booking_id)->first();

            if(!$booking_details) {

                throw new Exception(Helper::error_message(206), 206);
                
            }

            // Check the booking review completed already

            if($booking_details->status == BOOKING_COMPLETED) {
                
                throw new Exception(Helper::error_message(218), 218);
            }

            // Check the booking is eligible for review

            if($booking_details->status != BOOKING_CHECKOUT) {

                throw new Exception(Helper::error_message(214), 214);
                
            }

            // Check the user already rated

            $check_user_review = BookingUserReview::where('booking_id', $request->booking_id)->count();

            if($check_user_review) {

                throw new Exception(Helper::error_message(218), 218);
                
            }

            $review_details = new BookingUserReview;

            $review_details->user_id = $booking_details->user_id;

            $review_details->provider_id = $booking_details->provider_id;

            $review_details->host_id = $booking_details->host_id;

            $review_details->booking_id = $booking_details->id;

            $review_details->ratings = $request->ratings ?: 0;

            $review_details->review = $request->review ?: "";

            $review_details->status = APPROVED;

            if($review_details->save()) {

                DB::commit();

                $booking_details->status = BOOKING_COMPLETED;

                $booking_details->save();

                // Update total ratings & overall_ratings of host

                $host_details =  Host::find($booking_details->host_id);

                if($host_details) {

                    $host_details->total_ratings += 1;

                    $host_details->overall_ratings = BookingUserReview::where('host_id', $booking_details->host_id)->avg('ratings') ?: $host_details->overall_ratings;

                    $host_details->save();

                }

                $data = ['booking_id' => $request->booking_id, 'booking_provider_review_id' => $review_details->id];
                
                // Push Notification User Reviews
                $provider_details = Provider::where('id', $booking_details->provider_id)->VerifiedProvider()->first();

                if (Setting::get('is_push_notification') == YES && $provider_details) {

                    $title = $content = Helper::push_message(605);

                    $this->dispatch(new BellNotificationJob($booking_details, BELL_NOTIFICATION_TYPE_USER_REVIEW,$content,BELL_NOTIFICATION_RECEIVER_TYPE_PROVIDER,$booking_details->id,$booking_details->user_id,$booking_details->provider_id));

                    if($provider_details->push_notification_status == YES && ($provider_details->device_token != '')) {

                        $push_data = ['booking_id' => $request->booking_id, 'type' => PUSH_NOTIFICATION_REDIRECT_BOOKING_VIEW];
                       
                        PushRepo::push_notification($provider_details->device_token, $title, $content, $push_data, $provider_details->device_type);
                    }
                }


                if(Setting::get('is_email_notification') == YES && $provider_details) {

                    $email_data['subject'] = Setting::get('site_name').'-'.tr('reviews_updated_for_the_host', $booking_details->unique_id);

                    $email_data['page'] = "emails.providers.bookings.review";

                    $email_data['email'] = $provider_details->email;

                    $data['booking_details'] = $booking_details->toArray();
                    
                    $data['host_details'] = $booking_details->hostDetails->toArray() ?? [];

                    $email_data['data'] = $data;

                    $this->dispatch(new SendEmailJob($email_data));
                                        
                }

                $message = Helper::success_message(216); $code = 216; 

                return $this->sendResponse($message, $code, $data);
            }

            throw new Exception(Helper::error_message(219), 219);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());

        }

    }


    /**
     * @method bookings_checkin()
     *
     * @uses used to update the checkout status of booking
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param object $request
     *
     * @return response of details
     */

    public function bookings_checkin(Request $request){

        try {
            
            $validator = Validator::make($request->all(), [
                'booking_id' => 'required|exists:bookings,id,user_id,'.$request->id
            ]);

            if($validator->fails()){

                $error = implode(',', $validator->messages()->all());

                throw new Exception($error , 101);

            }
            
            $booking_details = Booking::where('bookings.id', $request->booking_id)->where('user_id', $request->id)->first();

            if(!$booking_details) {

                throw new Exception(Helper::error_message(206), 206);
            }

            // Check the booking is already checked-in

            if($booking_details->status == BOOKING_CHECKIN) {

                throw new Exception(Helper::error_message(222), 222);
                
            }

            // Check the booking is eligible for checkin

            if($booking_details->status != BOOKING_DONE_BY_USER) {

                throw new Exception(Helper::error_message(220), 220);
                
            }

            DB::beginTransaction();

            $booking_details->status = BOOKING_CHECKIN;

            $booking_details->checkin = date("Y-m-d H:i:s");

            if($booking_details->save()) {

                DB::commit();

                $provider_details = Provider::where('id', $booking_details->provider_id)->VerifiedProvider()->first();

                // Send notifications to user
                if (Setting::get('is_push_notification') == YES && $provider_details) {

                    $title = $content = Helper::push_message(606);

                    $this->dispatch(new BellNotificationJob($booking_details, BELL_NOTIFICATION_TYPE_CHECKIN,$content,BELL_NOTIFICATION_RECEIVER_TYPE_PROVIDER,$booking_details->id,$booking_details->user_id,$booking_details->provider_id));

                    if($provider_details->push_notification_status == YES && ($provider_details->device_token != '')) {

                        $push_data = ['booking_id' => $request->booking_id, 'type' => PUSH_NOTIFICATION_REDIRECT_BOOKING_VIEW];

                        PushRepo::push_notification($provider_details->device_token, $title, $content, $push_data, $provider_details->device_type);
                    }
                }


                if(Setting::get('is_email_notification') == YES && $provider_details) {

                    $email_data['subject'] = Setting::get('site_name').'-'.tr('user_checkin');

                    $email_data['page'] = "emails.users.bookings.checkin";

                    $email_data['email'] = $provider_details->email;

                    $data['booking_details'] = $booking_details->toArray();
                    
                    $data['host_details'] = $booking_details->hostDetails->toArray() ?? [];

                    $email_data['data'] = $data;

                    $this->dispatch(new SendEmailJob($email_data));
                                        
                }

                $data = ['booking_id' => $booking_details->id, 'checkin' => common_date($booking_details->checkin)];

                return $this->sendResponse(Helper::success_message(218), 218, $data);

            } else {

                throw new Exception(Helper::error_message(221), 221);
            }

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }

    /**
     * @method bookings_checkout()
     *
     * @uses used to update the checkout status of booking
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param object $request
     *
     * @return response of details
     */

    public function bookings_checkout(Request $request){

        try {

            $validator = Validator::make($request->all(), [

                'booking_id' => 'required|exists:bookings,id,user_id,'.$request->id
            ]);

            if($validator->fails()){

                $error = implode(',', $validator->messages()->all());

                throw new Exception($error , 101);

            }
            
            $booking_details = Booking::where('bookings.id', $request->booking_id)->first();

            if(!$booking_details) {

                throw new Exception(Helper::error_message(206), 206);
            }

            // check the booking is already checked-out

            if($booking_details->status == BOOKING_CHECKOUT) {

                throw new Exception(Helper::error_message(225), 225);
                
            }

            // check the booking is eligible for checkout

            if($booking_details->status != BOOKING_CHECKIN) {

                throw new Exception(Helper::error_message(223), 223);
                
            }

            DB::beginTransaction();

            $booking_details->status = BOOKING_CHECKOUT;

            $booking_details->checkout = date("Y-m-d H:i:s");

            if($booking_details->save()) {

                DB::commit();                

                $provider_details = Provider::where('id', $booking_details->provider_id)->VerifiedProvider()->first();

                // Send notifications to user
                if (Setting::get('is_push_notification') == YES && $provider_details) {

                    $title = $content = Helper::push_message(607);

                    $this->dispatch(new BellNotificationJob($booking_details, BELL_NOTIFICATION_TYPE_CHECKOUT,$content,BELL_NOTIFICATION_RECEIVER_TYPE_PROVIDER,$booking_details->id,$booking_details->user_id,$booking_details->provider_id));

                    if($provider_details->push_notification_status == YES && ($provider_details->device_token != '')) {

                        $push_data = ['booking_id' => $request->booking_id, 'type' => PUSH_NOTIFICATION_REDIRECT_BOOKING_VIEW];
                       
                        PushRepo::push_notification($provider_details->device_token, $title, $content, $push_data, $provider_details->device_type);
                    }
                }


                if(Setting::get('is_email_notification') == YES && $provider_details) {

                    $email_data['subject'] = Setting::get('site_name').'-'.tr('user_checkout');

                    $email_data['page'] = "emails.users.bookings.checkout";

                    $email_data['email'] = $provider_details->email;

                    $data['booking_details'] = $booking_details->toArray();
                    
                    $data['host_details'] = $booking_details->hostDetails->toArray() ?? [];

                    $email_data['data'] = $data;

                    $this->dispatch(new SendEmailJob($email_data));
                                        
                }


                $data = ['booking_id' => $booking_details->id, 'checkout' => common_date($booking_details->checkout), 'status_text' => booking_status($booking_details->status)];

                return $this->sendResponse(Helper::success_message(219), 219, $data);

            } else {

                throw new Exception(Helper::error_message(224), 224);
            }

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }
    /**
     * @method bookings_inbox()
     *
     * @uses used to get the list of bookings
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param object $request
     *
     * @return response of details
     */
    public function bookings_inbox(Request $request) {

        try {

            $chat_messages = ChatMessage::where('user_id' , $request->id)
                        ->select('host_id', 'provider_id', 'booking_id', 'type', 'type as chat_type', 'updated_at', 'message')
                        ->groupBy('host_id')
                        ->orderBy('updated_at' , 'desc')
                        ->skip($this->skip)
                        ->take($this->take)
                        ->get();

            foreach ($chat_messages as $key => $chat_message_details) {

                $provider_details = Provider::find($chat_message_details->provider_id);

                $chat_message_details->provider_name = $provider_details->name ?? "";

                $chat_message_details->provider_picture = $provider_details->picture ?? "";

                $chat_message_details->updated = $chat_message_details->updated_at->diffForHumans();
                
            }

            return $this->sendResponse($message = "", $code = "", $chat_messages);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }

    /**
     * @method bookings_chat_details()
     *
     * @uses used to get the messages for selected Booking
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param object $request
     *
     * @return response of details
     */
    public function bookings_chat_details(Request $request) {

        try {
            
            $validator = Validator::make($request->all(), 
                [
                    'host_id' => 'required', 
                    'provider_id' => 'required',
                    'booking_id' => $request->booking_id > 0 ? 'exists:bookings,id' : "", 
                ]
            );

            if($validator->fails()) {

                $error = implode(",", $validator->messages()->all());
                
                throw new Exception($error, 101);
            }

            $base_query = ChatMessage::select('chat_messages.id as chat_message_id', 'booking_id', 'host_id', 'provider_id', 'user_id', 'type', 'type as chat_type','updated_at', 'message');

            if($request->booking_id) {

                $base_query = $base_query->where('chat_messages.booking_id' , $request->booking_id);

            }

            if($request->host_id) {

                $base_query = $base_query->where('chat_messages.host_id' , $request->host_id);

            }

            if($request->provider_id) {

                $base_query = $base_query->where('chat_messages.provider_id' , $request->provider_id);

            }

            $chat_messages = $base_query->skip($this->skip)->take($this->take)
                                    ->orderBy('chat_messages.updated_at' , 'desc')
                                    ->get();

            foreach ($chat_messages as $key => $chat_message_details) {

                $provider_details = Provider::find($chat_message_details->provider_id);

                $chat_message_details->provider_name = $chat_message_details->provider_picture = "";

                $chat_message_details->updated = $chat_message_details->updated_at->diffForHumans();

                if($provider_details) {

                    $chat_message_details->provider_name = $provider_details->username;

                    $chat_message_details->provider_picture = $provider_details->picture;

                }
                
            }

            return $this->sendResponse($message = "", $code = "", $chat_messages);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }

    /**
     * @method bookings_upcoming()
     *
     * @uses used to get the list of bookings
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param object $request
     *
     * @return response of details
     */
    public function bookings_upcoming(Request $request) {
        
        try {

            $upcoming_status = [BOOKING_ONPROGRESS, BOOKING_DONE_BY_USER, BOOKING_CHECKIN];

            $booking_ids = Booking::where('bookings.user_id' , $request->id) 
                                ->whereIn('bookings.status', $upcoming_status)
                                ->skip($this->skip)->take($this->take)
                                ->orderBy('bookings.created_at', 'desc')
                                ->pluck('bookings.id');

            $bookings = BookingRepo::user_booking_list_response($booking_ids);

            return $this->sendResponse($message = "", $code = "", $bookings);
            
        } catch(Exception  $e) {
            
            return $this->sendError($e->getMessage(), $e->getCode());

        }

    } 

    /**
     * @method bookings_history ()
     *
     * @uses used to get the list of bookings
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param object $request
     *
     * @return response of details
     */
    public function bookings_history(Request $request) {

        try {

            $history_status = [BOOKING_CANCELLED_BY_USER, BOOKING_CANCELLED_BY_PROVIDER, BOOKING_COMPLETED, BOOKING_REFUND_INITIATED, BOOKING_CHECKOUT];

            $booking_ids = Booking::where('bookings.user_id' , $request->id) 
                            ->whereIn('bookings.status', $history_status)
                            ->orderBy('bookings.created_at', 'desc')
                            ->skip($this->skip)->take($this->take)
                            ->pluck('bookings.id');

            $bookings = BookingRepo::user_booking_list_response($booking_ids);

            return $this->sendResponse($message = "", $code = "", $bookings);

        } catch(Exception $e) {
            
            return $this->sendError($e->getMessage(), $e->getCode());

        }

    }

    /**
     * @method wishlist_list()
     *
     * @uses Get the user saved the hosts
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param object $request id
     *
     * @return response of details
     */
    public function wishlist_list(Request $request) {

        try {

            $wishlists = Wishlist::where('user_id' , $request->id)
                            ->skip($this->skip)
                            ->take($this->take)
                            ->CommonResponse()
                            ->get();

            foreach ($wishlists as $key => $wishlist_details) {

                $wishlist_details->wishlist_status = YES;

                $wishlist_details->per_day_formatted = formatted_amount($wishlist_details->per_day);

                $wishlist_details->per_day_symbol = tr('list_per_day_symbol');

                $wishlist_details->per_hour_formatted = formatted_amount($wishlist_details->per_hour);

                $wishlist_details->per_hour_symbol = tr('list_per_hour_symbol');
            }

            return $this->sendResponse($message = "", $code = "", $wishlists);

        } catch(Exception  $e) {
            
            return $this->sendError($e->getMessage(), $e->getCode());

        }

    }

    /**
     * @method wishlist_operations()
     *
     * @uses To add/Remove by using this operation favorite
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param object $request id, host_id
     *
     * @return response of details
     */
    public function wishlist_operations(Request $request) {

        try {

            DB::beginTransaction();

            $validator = Validator::make($request->all(),
                [
                    'clear_all_status' => 'in:'.YES.','.NO,
                    'host_id' => $request->clear_all_status == NO ? 'required|exists:hosts,id,status,'.APPROVED : '', 
                ], 
                [
                    'required' => Helper::error_message(200)
                ]
            );

            if($validator->fails()) {

                $error = implode(',', $validator->messages()->all());

                throw new Exception($error, 101);

            }

            if($request->clear_all_status == YES) {

                Wishlist::where('user_id', $request->id)->delete();
                
                DB::commit();

                return $this->sendResponse($message = Helper::success_message(202), $code = 202, $data = []);


            } else {

                $wishlist_details = Wishlist::where('host_id', $request->host_id)->where('user_id', $request->id)->first();

                if($wishlist_details) {

                    if($wishlist_details->delete()) {

                        DB::commit();

                        $data = ['wishlist_status' => NO, 'host_id' => $request->host_id];

                        return $this->sendResponse($message = Helper::success_message(201), $code = 201, $data);

                    } else {

                        throw new Exception(Helper::error_message(216), 216);
                      
                    }

                } else {

                    $wishlist_details = new Wishlist;

                    $wishlist_details->user_id = $request->id;

                    $wishlist_details->host_id = $request->host_id;

                    $wishlist_details->status = APPROVED;

                    $wishlist_details->save();

                    DB::commit();

                    $data = ['wishlist_id' => $wishlist_details->id, 'wishlist_status' => $wishlist_details->status, 'host_id' => $request->host_id];
               
                    return $this->sendResponse(Helper::success_message(200), 200, $data);

                }

            }

        } catch (Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }

    /**
     * @method requests_chat_history() @todo check and remove this function
     *
     * @uses used to get the messages list between user and provider
     *
     * @created Vidhya R
     *
     * @updated Vidhya R
     *
     * @param integer id, token
     *
     * @return json response 
     */

    public function requests_chat_history(Request $request) {

        $base_query = ChatMessage::where('user_id', $request->id)->where('request_id', $request->request_id);

        if($request->id) {

            ChatMessage::where('user_id', $request->id)
                ->where('request_id', $request->request_id)
                ->where('provider_id' , $request->provider_id)
                ->where('type' , 'pu')
                ->update(['delivered' => 1]);
        }

        $data = $base_query->get()->toArray();

        return $this->sendResponse($message = "", $code = "", $data);
    
    }

    /**
     * @method filter_options()
     *
     * @uses used to get the search options
     *
     * @created Vidhya R
     *
     * @updated Vidhya R
     *
     * @param integer id, token
     *
     * @return json response 
     */

    public function filter_options(Request $request) {

        $request->request->add(['device_type' => $this->device_type]);

        $search_options = ['SEARCH_OPTION_HOST_TYPE' => SEARCH_OPTION_HOST_TYPE, 'SEARCH_OPTION_PRICE' => SEARCH_OPTION_PRICE, 'SEARCH_OPTION_OTHER' => SEARCH_OPTION_OTHER];

        $data = $other_data = [];

        $dates_data = new \stdClass;

        $dates_data->title = tr('SEARCH_OPTION_DATE');

        $dates_data->description = "";

        $dates_data->search_type = SEARCH_OPTION_DATE;

        $dates_data->search_key = 'date';

        $dates_data->type = DATE;

        $dates_data->should_display_the_filter = YES;

        $dates_data->data = [];

        array_push($data, $dates_data);

        // Guests

        $guests_data = new \stdClass;

        $guests_data->title = tr('SEARCH_OPTION_GUEST');

        $guests_data->search_type = SEARCH_OPTION_GUEST;

        $guests_data->description = "";

        // $guests_data->search_key = '';

        $guests_data->type = INCREMENT_DECREMENT;

        $guests_data->should_display_the_filter = YES;

        $guests = HostHelper::filter_guests($request);

        $guests_data->data = $guests;

        array_push($data, $guests_data);

        if($this->device_type == DEVICE_WEB) {

            // Host type and price has come out from more filters

            // Host type

            $host_types_data = HostHelper::filter_options_host_type($request);

            array_push($data, $host_types_data);

            // | # | # | Pricings | # | # |
        
            $pricings_data = HostHelper::filter_options_pricings($request);

            array_push($data, $pricings_data);

            // | # | # | Pricings | # | # |

        }


        $search_data = new \stdClass;

        $search_data->search_type = SEARCH_OPTION_OTHER;

        $search_data->title = tr('SEARCH_OPTION_OTHER');

        $search_data->should_display_the_filter = YES;

        $other_filters = HostHelper::filter_options_others($request);

        // array_push($other_data, $other_filters);

        $search_data->data = $other_filters;

        array_push($data, $search_data);

        return $this->sendResponse($message = "", $code = "", $data);
    }

    /**
     * @method search_result()
     *
     * @uses used to get the search result based on the search key
     *
     * @created Vidhya R
     *
     * @updated Vidhya R
     *
     * @param
     *
     * @return json response 
     */

    public function search_result(Request $request) {

        $today = date('Y-m-d');

        try {

            // @todo proper validation 

            $validator = Validator::make($request->all(),[
                'search_type' => 'integer',
                'search_key' => '',
                'min_price'  => '',
                'max_price' => '',
                'on_off' => '',
                'from_date' => 'date|after:yesterday|bail',
                'to_date' => 'required_with:from_date|date|after:from_date|bail',
            ],
            [
                'from_date.after' => tr('date_equal_or_greater_today'),
                'to_date.required_with' => tr('to_date_required'),
                'to_date.after' => tr('to_date_greater_from_date'),
            ]);

            if($validator->fails()) {

                $error = implode(",", $validator->messages()->all());
                
                throw new Exception($error, 101);
            
            }

            $host_base_query = Host::VerifedHostQuery()
                                ->leftJoin('host_details','host_details.host_id' ,'=' , 'hosts.id')
                                ->orderBy('hosts.created_at', 'desc');

            // Based on the search type pass the conditions

            // Based on the inputs return lists

            // Location based search

            if($request->service_location_id) {

                $host_base_query = $host_base_query->where('hosts.service_location_id', $request->service_location_id);

            }

            // Dates

            if($request->from_date && $request->to_date) {

                if(strtotime($request->from_date) > strtotime($request->to_date)) {

                    throw new Exception(Helper::error_message(213, $request->from_date), 213);

                }

                $from_date = date("Y-m-d",strtotime($request->from_date));

                $to_date = date("Y-m-d",strtotime($request->to_date));

                $available_host_ids = HostAvailability::where('status', NOTAVAILABLE)
                                        ->whereBetween('host_availabilities.available_date', [$from_date, $to_date])
                                        ->pluck('host_id');

                if($available_host_ids) {

                    $host_base_query = $host_base_query->whereNotIn('hosts.id', $available_host_ids);
                }
            
            }

            // Price based 

            if($request->price) {

                $pricings = json_decode($request->price);

                if($pricings) {

                    $min_price = $pricings->min_input ?: 10.00;

                    $max_price = $pricings->max_input ?: 1000000.00;

                    if($min_price && $max_price) {

                        $host_base_query = $host_base_query->whereBetween('hosts.per_day',[$min_price, $max_price]);
      
                    }
                }
            }

            // Host type

            if($request->host_type) {

                $host_types = explode(',', $request->host_type);

                $host_base_query = $host_base_query->whereIn('hosts.host_type', $host_types);

            }

            // Guests count check

            if($request->adults || $request->children) {

                $total_guests = $request->adults + $request->children;

                $host_base_query = $host_base_query->where('host_details.total_guests', ">=", $total_guests);

            }

            // Amenties

            if($request->amenties) {

                $requested_amenities_ids = explode(',', $request->amenties);

                if($requested_amenities_ids) {

                    $amenities_questions = HostQuestionAnswer::UserSearchAmenities()->get();

                    $amenities_host_ids = [];

                    foreach ($amenities_questions as $key => $question_details) {

                        $host_amenities_ids = explode(',', $question_details->common_question_answer_id);

                        if(!empty(array_intersect($requested_amenities_ids, $host_amenities_ids))) {

                            $amenities_host_ids[] = $question_details->host_id;
                        }
                        
                    }

                    if($amenities_host_ids) {
                        
                        $host_base_query = $host_base_query->whereIn('hosts.id', $amenities_host_ids);

                    }

                }

            }

            // Rooms and beds 

            if($request->total_bedrooms) {

                $host_base_query = $host_base_query->where('host_details.total_bedrooms', ">=",$request->total_bedrooms);

            }

            if($request->total_bathrooms) {

                $host_base_query = $host_base_query->where('host_details.total_bathrooms', ">=",$request->total_bathrooms);

            }

            if($request->total_beds) {

                $host_base_query = $host_base_query->where('host_details.total_beds', ">=",$request->total_beds);

            }

            // sub category baased search 

            if($request->sub_category_id) {

                // Convert string into integer using array map and intval

                $sub_category_ids = array_map('intval', explode(',', $request->sub_category_id));

                if($sub_category_ids) {

                    $host_base_query = $host_base_query->whereIn('hosts.sub_category_id', $sub_category_ids);
                }

            }

            $host_ids = $host_base_query->skip($this->skip)->take($this->take)->pluck('hosts.id');

            $hosts = HostRepo::host_list_response($host_ids, $request->id);

            $hosts_data['title'] = tr('search_results');

            $hosts_data['description'] = "";

            $hosts_data['data'] = $hosts;

            $data = [];

            array_push($data, $hosts_data);

            return $this->sendResponse($message = "", $code = "", $data);
        
        } catch(Exception  $e) {
            
            return $this->sendError($e->getMessage(), $e->getCode());

        }

    }

    /**
     * @method filter_locations()
     *
     * @uses used get the related service location
     *
     * @created Vidhya R
     *
     * @updated Vidhya R
     *
     * @param
     *
     * @return json response 
     */

    public function filter_locations(Request $request) {

        try {

            $base_query = ServiceLocation::CommonResponse()->orderBy('service_locations.name', 'asc');

            if($request->location) {

                $base_query = $base_query->where('name', 'like', '%'.$request->location.'%');

            } else {

                $this->take = 6;
            }

            $service_locations = $base_query->skip($this->skip)->take($this->take)->get();

            return $this->sendResponse($message = "", $code = "", $service_locations);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        }     

    }

    /**
     * @method bell_notifications()
     *
     * @uses list of notifications for user
     *
     * @created vithya R
     *
     * @updated vithya R
     *
     * @param integer $id
     *
     * @return JSON Response
     */

    public function bell_notifications(Request $request) {

        try {

            $bell_notifications = BellNotification::where('to_id', $request->id)->where('receiver', USER)
                                        ->select('notification_type', 'booking_id', 'host_id', 'message', 'status as notification_status', 'from_id', 'to_id', 'receiver')
                                        ->get();

            foreach ($bell_notifications as $key => $bell_notification_details) {

                $picture = asset('placeholder.png');

                // if($bell_notification_details->notification_type == BELL_NOTIFICATION_NEW_SUBSCRIBER) {

                //     $user_details = User::find($bell_notification_details->from_id);

                //     $picture = $user_details ? $user_details->picture : $picture;

                // } else {

                //     $bell_notification_details = Host::find($bell_notification_details->host_id);

                //     $picture = $bell_notification_details ? $bell_notification_details->picture : $picture;

                // }

                $bell_notification_details->picture = $picture;

                unset($bell_notification_details->from_id);

                unset($bell_notification_details->to_id);
            }

            return $this->sendResponse($message = "", $code = "", $bell_notifications);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());

        }   
    
    }

    /**
     * @method bell_notifications_update()
     *
     * @uses list of notifications for user
     *
     * @created vithya R
     *
     * @updated vithya R
     *
     * @param integer $id
     *
     * @return JSON Response
     */

    public function bell_notifications_update(Request $request) {

        try {

            DB::beginTransaction();

            $bell_notifications = BellNotification::where('to_id', $request->id)->where('receiver', USER)->update(['status' => BELL_NOTIFICATION_STATUS_READ]);

            DB::commit();

            return $this->sendResponse(Helper::success_message(204), 204, $data = []);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());

        } 
    
    }

    /**
     * @method bell_notifications_count()
     * 
     * @uses Get the notification count
     *
     * @created vithya R
     *
     * @updated vithya R
     *
     * @param object $request - As of no attribute
     * 
     * @return response of boolean
     */
    public function bell_notifications_count(Request $request) {

        // TODO
            
        $bell_notifications_count = BellNotification::where('status', BELL_NOTIFICATION_STATUS_UNREAD)->where('receiver', USER)->where('to_id', $request->id)->count();

        $data = [];

        $data['count'] = $bell_notifications_count;

        return $this->sendResponse($message = "", $code = "", $data);
    }

    /**
     * @method host_price_calculator()
     * 
     * @uses calculate the total amount for user requested inputs
     *
     * @created vithya R
     *
     * @updated vithya R
     *
     * @param
     * 
     * @return response of boolean
     */
    public function host_price_calculator(Request $request) {

        try {

            $today = date('Y-m-d');

            // Formate checkin and checkout dates
            
            $checkin = $request->checkin ? common_date($request->checkin, $this->timezone = "" ,'Y-m-d') : "";

            $checkout = $request->checkout ? common_date($request->checkout, $this->timezone = "" ,'Y-m-d') : "";

            $request->request->add(['checkin' => $checkin, 'checkout' => $checkout]);

            $validator = Validator::make($request->all(), [
                'host_id' => 'required|exists:hosts,id,status,'.APPROVED,
                'checkin' => 'required|date|after:'.$today.'|bail',
                'checkout' => 'required_if:checkin,|date|after:checkin|bail',
                'adults' => 'min:1',
                'children' => 'min:0'
            ]);

            if($validator->fails()) {

                $error = implode(",", $validator->messages()->all());
                
                throw new Exception($error, 101);
                
            }

            // check the host details

            $host = Host::where('id', $request->host_id)->VerifedHostQuery()->first();

            $host_details = HostDetails::where('host_id', $request->host_id)->first();

            if(!$host || !$host_details) {

                throw new Exception(Helper::error_message(200), 200);
            }

            /** $ $ $ $ $  Min & Max values check start  $ $ $ $ $ */

            $total_guests = $request->adults + $request->children;

            // check the min & max guests validations

            if($total_guests < $host_details->min_guests) {

                throw new Exception("Required minimum guests:$host_details->min_guests", 101);
            }

            if($total_guests > $host_details->max_guests) {

                throw new Exception("The maximum guests is :$host_details->max_guests", 101);
            }

            // Check the dates are available

            if(strtotime($request->checkin) > strtotime($request->checkout)) {
                
                throw new Exception("The checkin date should be less than the checkout date", 101);

            }

            // Check min & Max stays

            $total_days = total_days($request->checkin, $request->checkout);

            if($total_days < $host->min_days) {
                throw new Exception("Required minimum nights:$host->min_days", 101);
            }

            if($total_days > $host->max_days) {
                throw new Exception("The maximum nights is :$host->max_days", 101);
            }

            // Check the host available on the selected dates

            $check_host_availablity = HostHelper::check_host_availablity($request->checkin, $request->checkout, $request->host_id);

            if($check_host_availablity == NO) {

                throw new Exception("The host is not available on the selected dates", 101);  
            }

            /** $ $ $ $ $  Min & Max values check end  $ $ $ $ $ */

            $data = new \stdClass;

            $data->host_id = $request->host_id;

            $data->checkin = $request->checkin;

            $data->checkout = $request->checkout;

            $data->checkin_time = $host->checkin ?: "Flexible";

            $data->checkout_time = $host->checkout ?: "Flexible";

            $data->adults = $request->adults;

            $data->children = $request->children;

            // Get adults + childrens count

            $data->per_day = formatted_amount($host->per_day);

            $data->per_day_symbol = tr('per_day_symbol');

            $data->min_guests = $host_details->min_guests;

            $data->max_guests = $host_details->max_guests;

            $data->total_guests = $total_guests;

            $data->total_days = $total_days." ".tr('per_day_text');

            $data->per_day = formatted_amount($host->per_day);

            $total_days_price = $host->per_day * $total_days;

            $data->total_days_price = formatted_amount($total_days_price);

            // Based on the min guests calculate the guest price

            $additional_guests = $total_guests > $host_details->min_guests ? $total_guests-$host_details->min_guests: 0;

            $total_additional_guest_price = $host->per_guest_price * $additional_guests;

            $data->per_guest_price = $host->per_guest_price ?? 0.00;

            $data->additional_guests = $additional_guests ?? 0;

            $data->total_additional_guest_price = formatted_amount($total_additional_guest_price);

            // Others

            $data->cleaning_fee = formatted_amount($host->cleaning_fee);

            $data->service_fee = $data->tax_price = formatted_amount(0);

            $data->total = formatted_amount((int)$total_days_price + (int) $host->cleaning_fee + (int) $host->service_fee + (int) $host->tax_price + (int)$total_additional_guest_price);

            Log::info("Host Price Calculator ------ ".print_r($data, true));

            return $this->sendResponse($message = "", $code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());

        }
    
    }

    /**
     * @method reviews_for_you()
     *
     * @uses used to get logged in user review
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param object $request
     *
     * @return response of details
     */
    public function reviews_for_you(Request $request) {

        try {

            $base_query = BookingProviderReview::where('booking_provider_reviews.user_id', $request->id)->CommonResponse();

            $reviews = $base_query->skip($this->skip)->take($this->take)->get();

            return $this->sendResponse($message = "", $code = "", $reviews);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }

    /**
     * @method reviews_for_providers()
     *
     * @uses used to get loggedin user rated reviews
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param object $request
     *
     * @return response of details
     */
    public function reviews_for_providers(Request $request) {

        try {

            $base_query = BookingUserReview::where('booking_user_reviews.user_id', $request->id)->CommonResponse();

            $reviews = $base_query->skip($this->skip)->take($this->take)->get();

            return $this->sendResponse($message = "", $code = "", $reviews);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }

    /**
     * @method vehicles_index()
     *
     * @uses used to user vehicles
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param object $request
     *
     * @return response of details
     */
    public function vehicles_index(Request $request) {

        try {

            $vehicles = UserVehicle::CommonResponse()->where('user_id', $request->id)->skip($this->skip)->take($this->take)->orderBy('user_vehicles.created_at', 'desc')->get();

            return $this->sendResponse($message = "", $success_code = "", $vehicles);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }

    /**
     * @method vehicles_save()
     *
     * @uses used to update/create the user vehicle details
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param object $request
     *
     * @return response of details
     */
    public function vehicles_save(Request $request) {

        try {

            // Common validator for all steps

            $validator = Validator::make($request->all(), [
                            'vehicle_type' => 'required',
                            'vehicle_number' => 'required',
                            'vehicle_brand' => 'required',
                            'vehicle_model' => 'required',
                            'user_vehicle_id' => 'exists:user_vehicles,id'
                        ]);

            if($validator->fails()) {

                $error = implode(',', $validator->messages()->all());

                throw new Exception($error , 101);

            }

            DB::beginTransaction();

            if($request->user_vehicle_id) {

                $vehicle_details = UserVehicle::where('id', $request->user_vehicle_id)->where('user_id', $request->id)->first();

            } else {

                $vehicle_details = new UserVehicle;

                $vehicle_details->user_id = $request->id;

            }

            $vehicle_details->vehicle_type = $request->vehicle_type;

            $vehicle_details->vehicle_number = $request->vehicle_number;

            $vehicle_details->vehicle_brand = $request->vehicle_brand;

            $vehicle_details->vehicle_model = $request->vehicle_model;

            $vehicle_details->save();

            DB::commit();

            $vehicle_details->user_vehicle_id = $vehicle_details->id;

            $message = "Vehicle Added";

            return $this->sendResponse($message, $success_code = "", $vehicle_details);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }

    /**
     * @method vehicles_delete()
     *
     * @uses used to get the reviews based review_type = provider | Host @todo 
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param object $request
     *
     * @return response of details
     */
    public function vehicles_delete(Request $request) {

        try {

            // Common validator for all steps

            $validator = Validator::make($request->all(), [
                            'user_vehicle_id' => 'exists:user_vehicles,id'
                        ]);

            if($validator->fails()) {

                $error = implode(',', $validator->messages()->all());

                throw new Exception($error , 101);

            }

            DB::beginTransaction();

            $vehicle_details = UserVehicle::where('id', $request->user_vehicle_id)->where('user_id', $request->id)->first();

            if(!$vehicle_details) {

                throw new Exception("Vehicle details not found", 101);
                
            }

            $vehicle_details->delete();

            DB::commit();

            $message = "Vehicle details deleted"; $code = 200;

            return $this->sendResponse($message, $code, $data = []);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }

    /**
     * @method home_map()
     *
     * @uses used to get the reviews based review_type = provider | Host @todo 
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param object $request
     *
     * @return response of details
     */
    public function home_map(Request $request) {

        try {

            $today = date('Y-m-d H:i:s', strtotime("+30 minutes"));

            // Formate checkin and checkout dates // @todo change the variable name

            if($request->checkin) {

                $checkin = common_date($request->checkin, $timezone = "", 'Y-m-d H:i:s');

                $request->request->add(['checkin' => $checkin]);

            }

            if($request->checkout) {

                $checkout = common_date($request->checkout, $timezone = "", 'Y-m-d H:i:s');

                $request->request->add(['checkout' => $checkout]);

            }

            // @todo Check the min duration between checkin and checkout

            $validator = Validator::make($request->all(),[
                'checkin' => 'bail|date|after:today',
                'checkout' => 'bail|required_if:checkin, |date|after:checkin',
                'latitude' => 'numeric',
                'longitude' => 'numeric|required_if:latitude, ',
            ]);

            if($validator->fails()) {

                $error = implode(",", $validator->messages()->all());
                
                throw new Exception($error, 101);
                
            }

            $base_query = Host::VerifedHostQuery()->where('hosts.total_spaces', '>', 0)->orderBy('hosts.updated_at', 'desc');

            if($request->latitude && $request->longitude) {

                $distance = Setting::get('search_radius', 100);

                $latitude = $request->latitude; $longitude = $request->longitude;

                $location_query = "SELECT hosts.id as host_id, 1.609344 * 3956 * acos( cos( radians('$latitude') ) * cos( radians(latitude) ) * cos( radians(longitude) - radians('$longitude') ) + sin( radians('$latitude') ) * sin( radians(latitude) ) ) AS distance FROM hosts
                                        WHERE (1.609344 * 3956 * acos( cos( radians('$latitude') ) * cos( radians(latitude) ) * cos( radians(longitude) - radians('$longitude') ) + sin( radians('$latitude') ) * sin( radians(latitude) ) ) ) <= $distance 
                                        ORDER BY distance";

                $location_hosts = DB::select(DB::raw($location_query));

                $location_host_ids = array_column($location_hosts, 'host_id');

                $base_query = $base_query->whereIn('hosts.id', $location_host_ids);

            }

            $l_host_ids = $base_query->pluck('hosts.id');

            // Get availability based hosts

            $hosts = Host::whereIn('id', $l_host_ids)->skip($this->skip)->take($this->take)->get();

            $host_ids = [];

            foreach ($hosts as $key => $host_details) {

                if(BookingRepo::host_availability_based_hosts($request->checkin, $request->checkout, $host_details)) {

                    $host_ids[] = $host_details->id;

                } else {

                    unset($hosts[$key]);
                }
            }

            $hosts = [];

            if($host_ids) {
                
                $hosts = HostRepo::park_hosts_list_response($host_ids, $request->id, $request);

            }

            return $this->sendResponse($message = "", $success_code = "", $hosts);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }

    /**
     * @method spaces_view()
     *
     * @uses  
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param object $request
     *
     * @return response of details
     */
    public function spaces_view(Request $request) {

        try {

            $host_details = Host::where('hosts.id', $request->host_id)->VerifedHostQuery()->UserParkFullResponse()->first();

            if(!$host_details) {

                throw new Exception(Helper::error_message(200), 200);
                
            }

            $host_details->total_bookings = Booking::where('host_id', $request->host_id)->count();

            $host_details->share_link = url('/');

            $host_details->wishlist_status = HostHelper::wishlist_status($request->host_id, $request->id);

            $host_details->per_hour_formatted = formatted_amount($host_details->per_hour);

            $host_details->gallery = HostGallery::where('host_id', $host_details->host_id)->select('picture', 'caption')->skip(0)->take(3)->get();

            $host_details->amenities = get_amenities($host_details->amenities, $host_details->host_type);

            return $this->sendResponse($message = "", $success_code = "", $host_details);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }

    /**
     * @method spaces_price_calculator()
     * 
     * @uses calculate the total amount for user requested inputs
     *
     * @created vithya R
     *
     * @updated vithya R
     *
     * @param
     * 
     * @return response of boolean
     */
    public function spaces_price_calculator(Request $request) {

        try {

            $today = date('Y-m-d H:i:s');

            // Formate checkin and checkout dates // @todo change the variable name

            $checkin = common_date($request->checkin, $timezone = "" ,'Y-m-d H:i:s');

            $checkout = common_date($request->checkout, $timezone = "" ,'Y-m-d H:i:s');

            $request->request->add(['checkin' => $checkin, 'checkout' => $checkout]);

            Log::info("spaces_price_calculator + + +".print_r($request->all(), true));

            $validator = Validator::make($request->all(),[
                'host_id' => 'required|exists:hosts,id,status,'.APPROVED,
                'checkin' => 'required|date',
                'checkout' => 'required_if:checkin,|date|after:checkin',
                'total_spaces' => 'min:1'
            ]);

            if($validator->fails()) {

                $error = implode(",", $validator->messages()->all());
                
                throw new Exception($error, 101);
                
            }

            // check the host details

            $host = Host::where('id', $request->host_id)->VerifedHostQuery()->first();

            // $host_details = HostDetails::where('host_id', $request->host_id)->first();

            if(!$host) {

                throw new Exception(Helper::error_message(200), 200);
            }

            // Check the dates are available

            if(strtotime($request->checkin) > strtotime($request->checkout)) {
                
                // throw new Exception("The checkin date should be less than the checkout date", 101);

            }

            $date_difference = date_convertion($request->checkin, $request->checkout);

            // Check the host available on the selected dates

            // $check_host_availablity = HostHelper::check_host_availablity($request->checkin, $request->checkout, $request->host_id);

            // if($check_host_availablity == NO) {

                // throw new Exception("The host is not available on the selected dates", 101);  
            // }

            $days = $date_difference->days ?: 0;

            $hours = $date_difference->hours ?: 0;

            $days_price = $host->per_day * $days;

            $hours_price = $host->per_hour * $hours;

            $total = $days_price + $hours_price;


            $data = new \stdClass;

            $data->host_id = $request->host_id;

            $data->checkin = $request->checkin;

            $data->checkout = $request->checkout;

            $data->duration = $date_difference->duration;


            $data->total_days = $days;

            $data->days_price = $days_price;

            $data->days_price = formatted_amount($days_price);


            $data->total_hours = $hours;

            $data->hours_price = $hours_price;

            $data->hours_price_formatted = formatted_amount($hours_price);


            $data->total = $total;

            $data->total_formatted = formatted_amount($total);


            $data->service_fee = formatted_amount(0);

            $data->tax_price = formatted_amount(0);

            Log::info("spaces_price_calculator data".print_r($data, true));

            return $this->sendResponse($message = "", $success_code = "", $data);

        } catch(Exception $e) {

            Log::info("spaces_price_calculator Exception".print_r($e->getMessage(), true));

            return $this->sendError($e->getMessage(), $e->getCode());

        }
    
    }

    /**
     * @method spaces_bookings_create()
     * 
     * @uses calculate the total amount for user requested inputs
     *
     * @created vithya R
     *
     * @updated vithya R
     *
     * @param
     * 
     * @return response of boolean
     */
    public function spaces_bookings_create(Request $request) {

        try {

            $today = date('Y-m-d H:i:s');

            // Formate checkin and checkout dates
            
            $checkin = $request->checkin ? common_date($request->checkin, "" ,'Y-m-d H:i:s') : "";

            $checkout = $request->checkout ? common_date($request->checkout, "" ,'Y-m-d H:i:s') : "";

            $request->request->add(['checkin' => $checkin, 'checkout' => $checkout]);

            $validator = Validator::make($request->all(),[
                'host_id' => 'required|exists:hosts,id',
                'checkin' => 'required|date|after:'.$today.'|bail',
                'checkout' => 'required_if:checkin,|date|after:checkin|bail',
                'user_vehicle_id' => 'required|exists:user_vehicles,id',
                'payment_mode' => 'required',
            ]);

            if($validator->fails()) {

                $error = implode(",", $validator->messages()->all());
                
                throw new Exception($error, 101);
                
            }

            // check the host details

            $host = Host::where('id', $request->host_id)->VerifedHostQuery()->first();

            if(!$host) {

                throw new Exception(Helper::error_message(200), 200);
            }

            // Check the payment mode and handle default card validation

            if($request->payment_mode == CARD) {

                // Check the user have default card

                $check_default_card = UserCard::where('user_id' , $request->id)->where('is_default', YES)->count();

                if($check_default_card == 0) {

                    throw new Exception(Helper::error_message(112), 112);
                    
                }

            }

            // Check the user already booked same place with same vehicle

            $is_same_vehicle_booked = BookingRepo::bookings_check_same_vehicle_same_space($request);

            if($is_same_vehicle_booked == YES) {

                throw new Exception(Helper::error_message(503), 503);

            }

            // Check the host is available or not

            $is_host_available = BookingRepo::host_availability_based_hosts($request->checkin, $request->checkout, $host);

            if($is_host_available == false) {

                throw new Exception(Helper::error_message(502), 502);
                
            }

            $date_difference = date_convertion($request->checkin, $request->checkout);

            $booking_response = BookingRepo::bookings_save($request, $host, $date_difference)->getData();
            
            $response_array = json_decode(json_encode($booking_response), true);

            return response()->json($response_array, 200);
            
        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());

        }
    
    }

}
