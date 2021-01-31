<?php

namespace App\Repositories;

use Illuminate\Http\Request;

use App\Helpers\Helper;

use Log, Setting, DB, Exception, FCM;

use LaravelFCM\Message\OptionsBuilder;

use LaravelFCM\Message\PayloadDataBuilder;

use LaravelFCM\Message\PayloadNotificationBuilder;

use App\User;

class PushNotificationRepository {

	/**
 	 * @method push_notification_android
 	 *
 	 * @uses Send Push Notification
 	 *
 	 * @created Vidhya
 	 * 
 	 * @updated Bhawya
 	 *
 	 * @param object $request
 	 *
 	 * @return 
 	 */

	public static function push_notification($register_ids , $title , $message , $push_data = [], $device_type = DEVICE_ANDROID, $is_user = NO) {

		if(Setting::get('is_push_notification') == NO) {

			Log::info("Push notification disabled by admin");

	  		return false;
		}

		if(!check_push_notification_configuration($is_user)) {

			Log::info("Push Notification configuration failed");

  			return false;

  		}

  		$title = Setting::get('site_name', 'RentCubo');

		if($device_type == DEVICE_ANDROID) {

  			self::push_notification_andriod($register_ids, $title, $message, $push_data, $is_user);

  		} else {

  			self::push_notification_andriod($register_ids, $title, $message, $push_data, $is_user);

  		} 	
 	}

	/**
 	 * @method send_push_notification()
 	 *
 	 * @uses Send Push Notification
 	 *
 	 * @created Vidhya
 	 * 
 	 * @updated Bhawya
 	 *
 	 * @param object $request
 	 *
 	 * @return 
 	 */

 	public static function push_notification_andriod($register_ids , $title , $message , $push_data = [], $is_user = YES) {

 		try {
 		
	 		// Check the register ids 

	 		if(!$register_ids || !$title || !$message) {

	 			return false;

	 		}

	 		if($is_user == NO) {

				config(['fcm.http.server_key' => Setting::get('provider_fcm_server_key')]);
				
				config(['fcm.http.sender_id' => Setting::get('provider_fcm_sender_id')]);

			} else {

				config(['fcm.http.server_key' => Setting::get('user_fcm_server_key')]);
				
				config(['fcm.http.sender_id' => Setting::get('user_fcm_sender_id')]);

			}

	 		$optionBuilder = new OptionsBuilder();

			$optionBuilder->setTimeToLive(60*20);

			$notificationBuilder = new PayloadNotificationBuilder($title);

			$notificationBuilder->setBody($message)->setSound('default');

			$dataBuilder = new PayloadDataBuilder();

			$dataBuilder->addData($push_data);

			Log::info("PUSH DATA".print_r($push_data, true));

			$option = $optionBuilder->build();

			$notification = $notificationBuilder->build();

			$data = $dataBuilder->build();

			$token = $register_ids;

			$downstreamResponse = FCM::sendTo($token, $option, $notification, $data);

			$downstreamResponse->numberSuccess();

			$downstreamResponse->numberFailure();

			$downstreamResponse->numberModification();

			//return Array - you must remove all this tokens in your database
			$downstreamResponse->tokensToDelete();

			//return Array (key : oldToken, value : new token - you must change the token in your database )
			$downstreamResponse->tokensToModify();

			//return Array - you should try to resend the message to the tokens in the array
			$downstreamResponse->tokensToRetry();

			// return Array (key:token, value:errror) - in production you should remove from your database the tokens

			Log::info("downstreamResponse Andrios".print_r($downstreamResponse , true));

			return true;

		} catch(Exception $e) {

			Log::info("Push notification Error".print_r($e->getMessage(), true));

			return false;
		}

 	}

 	/**
 	 * @method push_notification_ios
 	 *
 	 * @uses Send Push notification IOS
 	 *
 	 * @created Vidhya
 	 * 
 	 * @updated Bhawya
 	 *
 	 * @param object $request
 	 *
 	 * @return 
 	 */

 	public static function push_notification_ios($register_ids , $title , $message , $push_data = [], $is_user) {

 		// Check the register ids
 		
 		if(!$register_ids || !$title || !$message) {

 			return false;

 		}

 		if($is_user == NO) {

			config(['fcm.http.server_key' => Setting::get('provider_fcm_server_key')]);
			
			config(['fcm.http.sender_id' => Setting::get('provider_fcm_sender_id')]);

		} else {

			config(['fcm.http.server_key' => Setting::get('user_fcm_server_key')]);
			
			config(['fcm.http.sender_id' => Setting::get('user_fcm_sender_id')]);

		}

 		$optionBuilder = new OptionsBuilder();

		$optionBuilder->setTimeToLive(60*20);

		$notificationBuilder = new PayloadNotificationBuilder($title);

		$notificationBuilder->setBody($message)->setSound('default');

		$dataBuilder = new PayloadDataBuilder();

		$dataBuilder->addData(['data' => $push_data]);

		$option = $optionBuilder->build();
		$notification = $notificationBuilder->build();
		$data = $dataBuilder->build();

		$token = $register_ids;

		$downstreamResponse = FCM::sendTo($token, $option, $notification, $data);

		$downstreamResponse->numberSuccess();
		$downstreamResponse->numberFailure();
		$downstreamResponse->numberModification();

		//return Array - you must remove all this tokens in your database
		$downstreamResponse->tokensToDelete();

		//return Array (key : oldToken, value : new token - you must change the token in your database )
		$downstreamResponse->tokensToModify();

		//return Array - you should try to resend the message to the tokens in the array
		$downstreamResponse->tokensToRetry();

		// return Array (key:token, value:errror) - in production you should remove from your database the tokens

		Log::info("downstreamResponse IOS".print_r($downstreamResponse , true));

		return true;

 	
 	}
}