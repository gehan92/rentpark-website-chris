<?php

namespace App\Repositories;

use App\Helpers\Helper;

use App\Helpers\HostHelper;

use Log, Validator, Setting, DB, Exception;

use App\User, App\Provider, App\UserCard;

use App\Wishlist;

use App\Host, App\HostGallery, App\HostAvailability;

use App\Booking, App\BookingPayment, App\ProviderRedeem, App\UserRefund;


class BookingRepository {

    /**
     *
     * @method booking_list_response()
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

    public static function provider_booking_list_response($booking_ids, $provider_id = 0, $timezone = "") {

        $bookings = Booking::whereIn('bookings.id' , $booking_ids)
                            ->CommonResponse()
                            ->orderBy('bookings.id' , 'desc')
                            ->get();

        foreach ($bookings as $key => $booking_details) {

            $booking_details->checkin_time = common_date($booking_details->checkin, $timezone, "H:i A");
            
            $booking_details->checkout_time = common_date($booking_details->checkout, $timezone, "H:i A");


            $booking_details->checkin = common_date($booking_details->checkin, $timezone, 'd M Y');

            $booking_details->checkout = common_date($booking_details->checkout, $timezone, 'd M Y');
            $booking_details->buttons = booking_btn_status($booking_details->status, $booking_details->id, PROVIDER);

            $booking_details->total_formatted = formatted_amount($booking_details->total);

            $booking_details->status_text = booking_status($booking_details->status);

            $booking_details->buttons = booking_btn_status($booking_details->status, $booking_details->booking_id);

        }

        return $bookings;

    } 

    /**
     *
     * @method booking_list_response()
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

    public static function user_booking_list_response($booking_ids, $user_id = 0, $timezone = "") {

        $bookings = Booking::whereIn('bookings.id' , $booking_ids)
                            ->CommonResponse()
                            ->orderBy('bookings.created_at' , 'desc')
                            ->get();
        
        foreach ($bookings as $key => $booking_details) {

            $booking_details->wishlist_status = HostHelper::wishlist_status($booking_details->host_id, $booking_details->user_id);

            $booking_details->status_text = booking_status($booking_details->status);

            $booking_details->buttons = booking_btn_status($booking_details->status, $booking_details->booking_id);

            $booking_details->checkin_time = common_date($booking_details->checkin, $timezone, "H:i A");

            $booking_details->checkout_time = common_date($booking_details->checkout, $timezone, "H:i A");

            $booking_details->checkin = common_date($booking_details->checkin, $timezone, "d M Y");


            $booking_details->checkout = common_date($booking_details->checkout, $timezone, "d M Y");

            $booking_details->total_formatted = formatted_amount($booking_details->total);

        }
        return $bookings;

    } 

    /**
     *
     * @method check_booking_status()
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
    public static function check_booking_status($booking_details, $request) {

        // Check the checkin & checkin dates are updated

        $availability_step = $booking_details->availability_step; // checkin, checkout and total_guests

        if($booking_details->checkin && $booking_details->checkout && $booking_details->total_guests && $availability_step == NO) {
            $booking_details->availability_step = YES;
        }

        $basic_details_step = $booking_details->basic_details_step; // title, description

        if($booking_details->description && $basic_details_step == NO) {

            $booking_details->basic_details_step = YES;
    
        }

        $review_payment_step = $booking_details->review_payment_step;

        if($booking_details->payment_mode && $review_payment_step == NO && $request->step == 5) {

            $booking_details->review_payment_step = YES;

        }

        $booking_details->save();

    }	

    /**
     *
     * @method bookings_save()
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
    public static function bookings_save($request, $host, $date_difference) {

        DB::beginTransaction();

        $booking_details = new Booking;

        $booking_details->host_id = $request->host_id;

        $booking_details->user_id = $request->id;

        $booking_details->provider_id = $host->provider_id;

        $booking_details->user_vehicle_id = $request->user_vehicle_id;

        // $booking_details->host_checkin = $booking_details->host_checkout = "";

        $booking_details->description = $request->description ?: "";

        $booking_details->checkin = $request->checkin ? date('Y-m-d H:i:s', strtotime($request->checkin)) : $booking_details->checkin;

        $booking_details->checkout = $request->checkout ? date('Y-m-d H:i:s', strtotime($request->checkout)) : $booking_details->checkout;

        $booking_details->per_hour = $host->per_hour ?: 0.00;

        $booking_details->per_day = $host->per_day ?: 0.00;

        $booking_details->currency = Setting::get('currency', '$');

        $booking_details->payment_mode = $request->payment_mode ?: CARD;

        $days = $date_difference->days ?: 0;

        $hours = $date_difference->hours ?: 0;

        $days_price = $host->per_day * $days;

        $hours_price = $host->per_hour * $hours;

        $total = $days_price + $hours_price;


        $booking_details->total_days = total_days($request->checkin, $request->checkout);

        $booking_details->total = $total;

        $booking_details->duration = $date_difference->duration;


        $booking_details->save();

        $booking_payment_response = self::bookings_payment_by_stripe($request, $host, $booking_details)->getData();

        if($booking_payment_response->success == true) {
            
            $booking_details->status = BOOKING_DONE_BY_USER;

            $booking_details->save();

        } else {

            $booking_details->status = BOOKING_WAITING_FOR_PAYMENT;

            $booking_details->save();

            DB::commit();

            $response_array = ['success' => false, 'error' => $booking_payment_response->error, 'error_code' => $booking_payment_response->error_code];

            return response()->json($response_array, 200);

        }

        DB::commit();

        // Update the host availability

        self::host_availablity_update($booking_details->checkin, $booking_details->checkout, $host);

        $data = $booking_details->CommonResponse()->where('bookings.id', '=', $booking_details->id)->first();

        $data->total_formatted = formatted_amount($data->total);

        $data->checkin_time = common_date($booking_details->checkin, $request->timezone, "H:i A");

        $data->checkout_time = common_date($booking_details->checkout, $request->timezone, "H:i A");

        $data->checkin = common_date($booking_details->checkin, $request->timezone, "d M Y");

        $data->checkout = common_date($booking_details->checkout, $request->timezone, "d M Y");

        $response_array = ['success' => true, 'message' => Helper::success_message(203), 'code' => 203,'data' => $data];

        return response()->json($response_array, 200);

    } 

    /**
     *
     * @method bookings_payment_by_stripe
     *
     * @uses 
     *
     * @created
     *
     * @updated
     *
     * @param
     *
     * @return
     */

    public static function bookings_payment_by_stripe($request, $host_details, $booking_details) {

        try {

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

            $booking_payment->admin_amount = 0.00; $booking_payment->provider_amount = 0.00;

            $booking_payment->status = PAID;

            $booking_payment->save();

            // Commission spilit for bookings

            $commission_details = booking_commission_spilit($booking_details->total, $booking_payment);

            $booking_payment->admin_amount = $commission_details->admin_amount ?: 0.00;

            $booking_payment->provider_amount = $commission_details->provider_amount ?: 0.00;

            if($booking_payment->save()) {

                // Save Provider Redeem Amount
                
                self::provider_redeems_update($booking_payment->provider_id,$booking_payment->provider_amount);
            }

            $booking_payment->save();

            $response_array = ['success' => true];

            return response()->json($response_array, 200);

        } catch(Stripe_CardError | Stripe_InvalidRequestError | Stripe_AuthenticationError | Stripe_ApiConnectionError | Stripe_Error $e) {         

            Log::info("error".$e->getMessage());

            $response_array = ['success' => false, 'error' => $e->getMessage(), 'error_code' => $e->getCode()];

            return response()->json($response_array, 200);


        } catch(Exception $e) {

            Log::info("error".$e->getMessage());

            $response_array = ['success' => false, 'error' => $e->getMessage(), 'error_code' => $e->getCode()];

            return response()->json($response_array, 200);

        }

    }

    /**
     *
     * @method revert_host_availability()
     *
     * @uses update the host availability 
     *
     * @created vithya R
     * 
     * @updated vithya R
     *
     * @param 
     *
     * @return
     */
    
    public static function revert_host_availability($booking_details) {

        $checkin = $booking_details->checkin;

        $checkout = $booking_details->checkout;

        $host_availabilities = HostAvailability::where('host_id', $booking_details->host_id)->whereBetween('available_date', [$checkin, $checkout])->delete();

        Log::info("revert_host_availability".print_r(count($host_availabilities), true));

        return true;
    }


    /**
     *
     * @method booking_update_host_availability()
     *
     * @uses update the host availability 
     *
     * @created vithya R
     * 
     * @updated vithya R
     *
     * @param 
     *
     * @return
     */
    
    public static function booking_update_host_availability($booking_details) {

        $checkin = date('Y-m-d', strtotime($booking_details->checkin));

        $checkout = date('Y-m-d', strtotime($booking_details->checkout));

        $host_availabilities = HostAvailability::where('host_id', $booking_details->host_id)->whereBetween('available_date', [$checkin, $checkout])->update(['is_blocked_booking' => YES, 'status' => DATE_NOTAVAILABLE, 'checkin_status' => DATE_NOTAVAILABLE]);


        $valid_dates = generate_between_dates($checkin, $checkout, $format = "Y-m-d");

        $valid_dates[] = $checkout;

        foreach ($valid_dates as $key => $requested_date) {

            $check_host_availablity = $host_availablity = HostAvailability::where('host_id', $booking_details->host_id)->whereDate('available_date', $requested_date)->first();

            if(!$check_host_availablity) {

                $host_availablity = new HostAvailability;

            }

            $host_availablity->provider_id = $booking_details->provider_id;

            $host_availablity->host_id = $booking_details->host_id;

            $host_availablity->available_date = date('Y-m-d', strtotime($requested_date));

            $host_availablity->status = $host_availablity->checkin_status = DATE_NOTAVAILABLE;

            $host_availablity->is_blocked_booking = YES;

            $host_availablity->save();

        }

        Log::info("booking_update_host_availability".print_r(count($host_availabilities), true));

        return true;

    }

    /**
     * @method update_provider_reedems()
     *
     * @uses 
     *
     * @created Bhawya
     *
     * @updated Bhawya
     *
     * @param boolean $provider_id,$amount
     *
     * @return boolean response
     */
    public static function provider_redeems_update($provider_id,$total_amount) {

        try {

            DB::beginTransaction();

            $provider_details = Provider::find($provider_id);

            if(!$provider_details) {
     
                throw new Exception(Helper::error_message(1006) , 1006);

            }

            $provider_redeems = ProviderRedeem::where('provider_id',$provider_id)->first() ?? new ProviderRedeem;

            $provider_redeems->provider_id = $provider_id;

            $provider_redeems->total += $total_amount;

            $provider_redeems->remaining_amount += $total_amount;
            
            $provider_redeems->save();
            
            DB::commit();

        } catch(Exception $e) {

            DB::rollback();

            $response = ['success' => false, 'error' => $e->getMessage(), 'error_code' => $e->getCode()];

            return response()->json($response, 200);

        }      

    }

    /**
     * @method revert_provider_redeems()
     *
     * @uses 
     *
     * @created Bhawya
     *
     * @updated Bhawya
     *
     * @param boolean $provider_id,$amount
     *
     * @return boolean response
     */
    public static function revert_provider_redeems($booking_details) {

        try {

            DB::beginTransaction();

            $provider_details = Provider::find($booking_details->provider_id);

            if(!$provider_details) {
     
                throw new Exception(Helper::error_message(1006) , 1006);

            } 

            $provider_redeem_details = ProviderRedeem::where('provider_id',$booking_details->provider_id)->first();
            
            if(!$provider_redeem_details) {
     
                throw new Exception(Helper::error_message(229) , 229);

            } 

            // Take the provider amount from bookings paymetns
            $booking_user_paid_amount = $booking_details->bookingPayments->provider_amount ?? 0;
           
            $provider_deduction_amount = $provider_redeem_details->remaining_amount - $booking_user_paid_amount;
            
            if($provider_deduction_amount <= 0) {

                $provider_redeem_details->remaining_amount = $provider_deduction_amount;

                $provider_redeem_details->dispute_amount += $booking_user_paid_amount;

            } else {

                $provider_redeem_details->remaining_amount = $provider_redeem_details->remaining_amount - $booking_user_paid_amount;

                $provider_redeem_details->dispute_amount += $booking_user_paid_amount;

            }

            $provider_redeem_details->save();
                
            DB::commit();

        } catch(Exception $e) {

            DB::rollback();

            $response = ['success' => false, 'error' => $e->getMessage(), 'error_code' => $e->getCode()];

            return response()->json($response, 200);

        }

    }

    /**
     * @method add_user_refund()
     *
     * @uses 
     *
     * @created Bhawya
     *
     * @updated Bhawya
     *
     * @param boolean $provider_id,$amount
     *
     * @return boolean response
     */
    public static function add_user_refund($booking_details) {

        try {

            DB::beginTransaction();

            $user_details = User::find($booking_details->user_id);
        
            if(!$user_details) {
     
                throw new Exception(Helper::error_message(1002) , 1002);

            } 

            $user_refunds = UserRefund::where('user_id',$booking_details->user_id)->first() ?? new UserRefund;

            $user_refunds->user_id = $booking_details->user_id;

            $user_refunds->total += $booking_details->total;

            $user_refunds->remaining_amount += $booking_details->total;
            
            $user_refunds->save();

            DB::commit();

        } catch(Exception $e) {
            DB::rollback();

            $response = ['success' => false, 'error' => $e->getMessage(), 'error_code' => $e->getCode()];

            return response()->json($response, 200);

        }

    }

    /**
     *
     * @method host_availability_based_hosts()
     *
     * @uses
     *
     * @created Vidhya R
     * 
     * @updated Vidhya R
     *
     * @param
     *
     * @return 
     */

    public static function host_availability_based_hosts($checkin, $checkout, $host_details) {

        // Step 1: check the given checkin & checkout days are available on the host available days

        // Check the available days

        $checkin_day = date('N', strtotime($checkin));

        $checkout_day = date('N', strtotime($checkout));

        // Get host available days

        $available_days = explode(',', $host_details->available_days);

        if(!$available_days) {

            return false;
        }

        if(!in_array($checkin_day, $available_days) || !in_array($checkin_day, $available_days)) {

            return false;
        }

        // Check the host have any record in availabiity, if no means host is available

        $count_host_availablity = HostAvailability::where('host_id', $host_details->id)->count();

        if(!$count_host_availablity) {

            return true;

        }

        $total_spaces = $host_details->total_spaces ?? 0;

        // Step 2: Check the checkin and checkout is same date

        if(strtotime(date('Y-m-d', strtotime($checkin))) == strtotime(date('Y-m-d', strtotime($checkout)))) {

            // Check the availability data 

            $check_host_availablity = HostAvailability::where('host_id', $host_details->id)->where('date', date('Y-m-d', strtotime($checkin)))->count();

            // If there no availability means NO BOOKINGS. so we can directly assign the host as available

            if(!$check_host_availablity) {

                return true;
            }

            // Get the checkin slot 

            $checkin_slot = get_time_slot(date('H:i:s', strtotime($checkin)));

            // Checkin slot is have enough space

            $checkin_host_availability = HostAvailability::where('host_id', $host_details->id)->where('slot', $checkin_slot)->where('date', date('Y-m-d', strtotime($checkin)))->first();

            if($checkin_host_availability) {

                if($checkin_host_availability->remaining_spaces <= 0) {

                    return false;
                }
            }

            // Get the checkout slot

            $checkout_slot = get_time_slot(date('H:i:s', strtotime($checkout)));

            // Checkin slot is have enough space

            $checkout_host_availability = HostAvailability::where('host_id', $host_details->id)->where('slot', $checkout_slot)->whereDate('date', date('Y-m-d', strtotime($checkout)))->first();

            if($checkout_host_availability) {
                
                if($checkout_host_availability->remaining_spaces <= 0) {
                    return false;
                }
            }

            // Check the slots between checkin and checkout

            for ($slot=$checkin_slot; $slot <= $checkout_slot ; $slot++) { 

                $slot_host_availability = HostAvailability::where('host_id', $host_details->id)->where('slot', $checkout_slot)->whereDate('date', date('Y-m-d', strtotime($checkout)))->first();

                if($slot_host_availability) {
                
                    if($slot_host_availability->remaining_spaces <= 0) {
                        return false;
                    }
                }
                
            }

        } else {

            $checkin = date('Y-m-d H'.":00:00", strtotime($checkin));

            $checkout = date('Y-m-d H'.":00:00", strtotime($checkout));

            $period = new \DatePeriod(
                     new \DateTime($checkin),
                     new \DateInterval('PT1H'),
                     new \DateTime($checkout)
                );

            foreach ($period as $key => $value) {

                $current_date = $value->format('Y-m-d');

                $current_time = $value->format('H'.':00:00');

                $current_slot = get_time_slot($current_time);

                // Checkin slot is have enough space

                $checkin_host_availability = HostAvailability::where('host_id', $host_details->id)->where('slot', $current_slot)->where('date', $current_date)->first();

                if($checkin_host_availability) {

                    if($checkin_host_availability->remaining_spaces <= 0) {

                        return false;
                    }
                }

            }

        }

        return true;

    }

    /**
     *
     * @method host_availablity_update()
     *
     * @uses
     *
     * @created
     *
     * @updated
     *
     * @param
     *
     * @return
     */
    public static function host_availablity_update($checkin, $checkout, $host_details) {

        try {

            $checkin = date('Y-m-d H'.":00:00", strtotime($checkin));

            $checkout = date('Y-m-d H'.":00:00", strtotime($checkout));

            $period = new \DatePeriod(
                     new \DateTime($checkin),
                     new \DateInterval('PT1H'),
                     new \DateTime($checkout)
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

                $host_availablity->remaining_spaces -= 1;

                $host_availablity->used_spaces += 1;

                $host_availablity->save();

            }

        } catch(Exception $e) {

            Log::info("host_availablity_update - error".print_r($e->getMessage(), true));

        }

    }

    /**
     * @method bookings_check_same_vehicle_same_space()
     *
     * @uses
     *
     * @created
     *
     * @updated
     *
     * @param
     *
     * @return
     *
     */
    
    public static function bookings_check_same_vehicle_same_space($request) {

        $checkin = new \DateTime($request->checkin);

        $in = new \DateInterval('PT1H'); // '03:05:01';

        $checkin->add($in);
        
        $plus_checkin = $checkin->format('Y-m-d H:i:s');

        $checkout = new \DateTime($request->checkout);

        $out = new \DateInterval('PT1H'); // '03:05:01';
         
        $checkout->add($out);
        
        $plus_checkout = $checkout->format('Y-m-d H:i:s');

        $check_status = [BOOKING_ONPROGRESS, BOOKING_WAITING_FOR_PAYMENT, BOOKING_DONE_BY_USER, BOOKING_CHECKIN];

        $check_bookings = Booking::where('user_id', $request->id)
                            ->where('user_vehicle_id', $request->user_vehicle_id)
                            ->whereBetween('checkin', [$request->checkin, $plus_checkin])
                            ->orWhereBetween('checkout', [$request->checkout, $plus_checkout])
                            ->whereIn('status', $check_status)
                            ->count();

        return $check_bookings ? YES : NO;

    } 
	
}