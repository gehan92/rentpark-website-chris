<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */

Route::get('/settings', function () {

    $jsonString = file_get_contents(public_path('default-json/settings.json'));

    $data = json_decode($jsonString, true);

    return $data;
});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('pages/list', 'ApplicationController@static_pages_api');

Route::any('/chat_messages/save', 'ApplicationController@chat_messages_save');

Route::post('/chat_messages/update/status', 'ApplicationController@chat_messages_update_status')->name("update.message.status");

Route::any('categories', 'ApplicationController@categories');

Route::any('sub_categories', 'ApplicationController@sub_categories');

Route::get('/email/verify', 'ApplicationController@email_verify')->name('email.verify');

// User api's

Route::group(['prefix' => 'user'], function () {

    Route::any('categories', 'ApplicationController@categories');

    Route::any('sub_categories', 'ApplicationController@sub_categories');

    Route::post('home', 'UserApiController@home');

    /***
     *
     * User Account releated routs
     *
     */

    Route::post('/register', 'UserApiController@register');

    Route::post('/login', 'UserApiController@login');

    Route::post('/forgot_password', 'UserApiController@forgot_password');

    Route::group(['middleware' => 'UserApiVal'], function () {

        Route::post('/profile', 'UserApiController@profile'); // 1

        Route::post('/update_profile', 'UserApiController@update_profile'); // 2

        Route::post('/change_password', 'UserApiController@change_password'); // 3

        Route::post('/delete_account', 'UserApiController@delete_account'); // 4

        Route::post('/push_notification_update', 'UserApiController@push_notification_status_change'); // 5

        Route::post('/email_notification_update', 'UserApiController@email_notification_status_change'); // 6

        Route::post('/logout', 'UserApiController@logout'); // 7

        // CARDS curd Operations

        Route::post('cards_add', 'UserApiController@cards_add'); // 15

        Route::post('cards_list', 'UserApiController@cards_list'); // 16

        Route::post('cards_delete', 'UserApiController@cards_delete'); // 17

        Route::post('cards_default', 'UserApiController@cards_default'); // 18
        
        Route::post('payment_mode_default', 'UserApiController@payment_mode_default'); // 18

        Route::post('payment_mode_default', 'UserApiController@payment_mode_default'); // 18

        //configurations

        Route::post('notification/settings', 'UserApiController@notification_settings'); // 22

        // Reviews

        Route::post('reviews_for_you', 'UserApiController@reviews_for_you');

        Route::post('reviews_for_providers', 'UserApiController@reviews_for_providers');

        Route::post('/update_billing_info', 'UserApiController@update_billing_info');

        Route::post('/billing_info', 'UserApiController@billing_info');

    });

    Route::post('/project/configurations', 'UserApiController@configurations');

    Route::get('pages/list', 'ApplicationController@static_pages_api');

    // Core api's

    Route::post('suggestions', 'UserApiController@suggestions');

    Route::post('filter_options', 'UserApiController@filter_options');

    Route::post('filter_locations', 'UserApiController@filter_locations');

    Route::post('search_result', 'UserApiController@search_result');

    Route::post('home_first_section', 'UserApiController@home_first_section');

    Route::post('home_second_section', 'UserApiController@home_second_section');

    Route::post('see_all', 'UserApiController@see_all_section');

    Route::post('hostings_category_based', 'UserApiController@hostings_category_based');

    Route::post('hostings_sub_category_based', 'UserApiController@hostings_sub_category_based');

    Route::post('spaces_view' , 'UserApiController@spaces_view');

    Route::post('hosts_availability' , 'UserApiController@hosts_availability');

    Route::post('reviews' , 'UserApiController@reviews_index');
    
    Route::post('providers_view' , 'UserApiController@providers_view');


    Route::post('providers_view', 'UserApiController@providers_view');

    Route::post('users_view', 'UserApiController@other_users_view');

    Route::post('home_map' , 'UserApiController@home_map');

    // Price calculator
    
    Route::post('host_price_calculator' , 'UserApiController@host_price_calculator');
    
    Route::post('spaces_price_calculator' , 'UserApiController@spaces_price_calculator');

    Route::post('host_price_calculator', 'UserApiController@host_price_calculator');

    Route::group(['middleware' => 'UserApiVal'], function () {

        // Wishlist

        Route::post('wishlist', 'UserApiController@wishlist_list');

        Route::post('wishlist_operations', 'UserApiController@wishlist_operations');

        // Pre bookings routes

        Route::post('bookings_steps_info', 'UserApiController@bookings_steps_info');

        Route::post('bookings_create', 'UserApiController@bookings_create');

        Route::post('spaces_bookings_create' , 'UserApiController@spaces_bookings_create');

        // Post bookings

        Route::post('bookings_view', 'UserApiController@bookings_view');

        Route::post('bookings_history', 'UserApiController@bookings_history');

        Route::post('bookings_upcoming', 'UserApiController@bookings_upcoming');

        Route::post('bookings_inbox', 'UserApiController@bookings_inbox');

        Route::post('bookings_chat_details', 'UserApiController@bookings_chat_details');

        Route::post('bookings_cancel', 'UserApiController@bookings_cancel');

        Route::post('bookings_rating_report', 'UserApiController@bookings_rating_report');

        Route::post('bookings_checkin', 'UserApiController@bookings_checkin');

        Route::post('bookings_checkout', 'UserApiController@bookings_checkout');

        // message history API

        Route::get('/message/get', 'UserApiController@requests_chat_history');

        // Notification

        Route::post('bell_notifications/', 'UserApiController@bell_notifications');

        Route::post('bell_notifications/update', 'UserApiController@bell_notifications_update');

        Route::post('bell_notifications/count', 'UserApiController@bell_notifications_count');

        Route::post('vehicles', 'UserApiController@vehicles_index');

        Route::post('vehicles_save', 'UserApiController@vehicles_save');

        Route::post('vehicles_delete', 'UserApiController@vehicles_delete');



    });

    Route::any('static_pages_web', 'ApplicationController@static_pages_web');

});

// Provider api's

Route::group(['prefix' => 'provider'], function () {
// Route::group(['prefix' => 'provider'], function() {

    Route::any('categories', 'ApplicationController@categories');

    /***
     *
     * Provider Account releated routs
     *
     */

    Route::post('/register', 'ProviderApiController@register');

    Route::post('/login', 'ProviderApiController@login');

    Route::post('/forgot_password', 'ProviderApiController@forgot_password');

    Route::group(['middleware' => 'ProviderApiVal'], function () {

        Route::post('/dashboard', 'ProviderApiController@dashboard'); // 1
        // Revenues

        Route::post('transactions_history', 'ProviderApiController@transactions_history');

        Route::post('/profile', 'ProviderApiController@profile'); // 1

        Route::post('/update_profile', 'ProviderApiController@update_profile'); // 2

        Route::post('/change_password', 'ProviderApiController@change_password'); // 3

        Route::post('/delete_account', 'ProviderApiController@delete_account'); // 4

        Route::post('/logout', 'ProviderApiController@logout'); // 7

        // Reviews

        Route::post('reviews_for_you', 'ProviderApiController@reviews_for_you');

        Route::post('reviews_for_users', 'ProviderApiController@reviews_for_users');

        // Settings

        Route::post('/push_notification_update', 'ProviderApiController@push_notification_status_change'); // 5

        Route::post('/email_notification_update', 'ProviderApiController@email_notification_status_change'); // 6

        Route::post('/update_billing_info', 'ProviderApiController@update_billing_info');

        Route::post('/billing_info', 'ProviderApiController@billing_info');

        // Search api's

        Route::post('search', 'ProviderApiController@search');

        Route::post('search/all', 'ProviderApiController@search_all')->name('api.search.all');

        // CARDS curd Operations

        Route::post('cards_list', 'ProviderApiController@cards_list'); // 16

        Route::post('cards_add', 'ProviderApiController@cards_add'); // 15

        Route::post('cards_default', 'ProviderApiController@cards_default'); // 18

        Route::post('cards_delete', 'ProviderApiController@cards_delete'); // 17

        // Subscriptions management

        Route::post('subscriptions', 'ProviderApiController@subscriptions');

        Route::post('subscriptions_payment_by_stripe', 'ProviderApiController@subscriptions_payment_by_stripe');

        Route::post('subscriptions_history', 'ProviderApiController@subscriptions_history');

        //configurations

        Route::post('notification/settings', 'ProviderApiController@notification_settings'); // 22

        // Hosts lists

        Route::post('hosts_configuration', 'ProviderApiController@hosts_configuration');

        Route::any('sub_categories', 'ProviderApiController@sub_categories');

        Route::post('hosts_steps', 'ProviderApiController@hosts_steps');

        Route::post('hosts_index', 'ProviderApiController@hosts_index');

        Route::post('hosts_save', 'ProviderApiController@hosts_save');

        Route::post('hosts_upload_files', 'ProviderApiController@hosts_upload_files');

        Route::post('hosts_remove_files', 'ProviderApiController@hosts_remove_files');

        Route::post('hosts_galleries', 'ProviderApiController@hosts_galleries');

        Route::post('hosts_status', 'ProviderApiController@hosts_status');

        Route::post('hosts_delete', 'ProviderApiController@hosts_delete');

        Route::post('hosts_view', 'ProviderApiController@hosts_view');

        // Spaces 

        Route::post('hosts_galleries', 'ProviderApiController@hosts_galleries');
        
        Route::post('spaces_configurations', 'ProviderApiController@spaces_configurations');

        Route::post('spaces_save', 'ProviderApiController@spaces_save');

        Route::post('spaces_view', 'ProviderApiController@spaces_view');

        Route::post('spaces_availability_list', 'ProviderApiController@spaces_availability_list');

        Route::post('spaces_availabilities_delete', 'ProviderApiController@spaces_availabilities_delete');

        // Availability

        Route::post('hosts_availability', 'ProviderApiController@hosts_availability');

        Route::post('hosts_set_availability', 'ProviderApiController@hosts_set_availability');

        
        Route::post('host_availability_lists' , 'ProviderApiController@host_availability_lists');

        Route::post('host_availability_list_save' , 'ProviderApiController@host_availability_list_save');

        Route::post('host_availability_list_delete' , 'ProviderApiController@host_availability_list_delete');

        // Post bookings

        Route::post('bookings_view', 'ProviderApiController@bookings_view');

        Route::post('bookings_history', 'ProviderApiController@bookings_history');

        Route::post('bookings_upcoming', 'ProviderApiController@bookings_upcoming');

        Route::post('bookings_inbox', 'ProviderApiController@bookings_inbox');

        Route::post('bookings_chat_details', 'ProviderApiController@bookings_chat_details');

        Route::post('bookings_cancel', 'ProviderApiController@bookings_cancel');

        Route::post('bookings_rating_report', 'ProviderApiController@bookings_rating_report');

        // Route::post('/message/get', 'ProviderApiController@requests_chat_history');

        // Notification

        Route::post('bell_notifications/', 'ProviderApiController@bell_notifications');

        Route::post('bell_notifications/update', 'ProviderApiController@bell_notifications_update');

        Route::post('bell_notifications/count', 'ProviderApiController@bell_notifications_count');

    });

    Route::post('providers_view', 'ProviderApiController@providers_view'); // Not yet

    Route::post('users_view', 'ProviderApiController@users_view');

    Route::post('/project/configurations', 'ProviderApiController@configurations');

    Route::get('pages/list', 'ApplicationController@static_pages_api');

    Route::any('service_locations' , 'ApplicationController@service_locations');

    Route::any('static_pages_web' , 'ApplicationController@static_pages_web');


});
