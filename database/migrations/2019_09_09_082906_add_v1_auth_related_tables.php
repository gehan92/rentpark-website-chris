<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddV1AuthRelatedTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Users
        if (!Schema::hasTable('users')) {
            
            Schema::create('users', function (Blueprint $table) {
                $table->increments('id');
                $table->string('unique_id');
                $table->string('username');
                $table->string('first_name');
                $table->string('last_name');
                $table->string('name');
                $table->string('email')->unique();
                $table->string('token');
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password');
                $table->string('dob')->default("");
                $table->text('description')->nullable();
                $table->string('mobile')->default("");
                $table->string('picture')->default(envfile('APP_URL')."/placeholder.jpg");
                $table->string('token_expiry');
                $table->tinyInteger('user_type')->default(0);
                $table->integer('language_id')->default(0);
                $table->string('device_token')->default('');
                $table->enum('device_type',array('web','android','ios'));
                $table->enum('register_type',array('web','android','ios'));
                $table->enum('login_by',array('manual','facebook','google','twitter' , 'linkedin'));
                $table->string('social_unique_id')->default('');
                $table->enum('gender',array('male','female','others'));
                $table->string('payment_mode');
                $table->string('user_card_id')->default(0);
                $table->string('timezone')->default('America/Los_Angeles');
                $table->tinyInteger('registration_steps')->default(0);
                $table->integer('push_notification_status')->default(1);
                $table->integer('email_notification_status')->default(1);
                $table->integer('is_verified')->default(0);
                $table->string('verification_code')->default('');
                $table->string('verification_code_expiry')->default('');
                $table->tinyInteger('status')->default(0);
                $table->rememberToken();
                $table->timestamps();
            
            });
        }

        if (!Schema::hasTable('user_cards')) {

            Schema::create('user_cards', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('user_id');
                $table->string('card_name');
                $table->string('customer_id');
                $table->string('last_four');
                $table->string('card_token');
                $table->string('is_default')->default(0);
                $table->tinyInteger('status')->default(1);
                $table->timestamps();
            });
        
        }

        if(!Schema::hasTable('user_refunds')) {

            Schema::create('user_refunds', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('user_id');
                $table->float('total')->default(0.00);
                $table->float('paid_amount')->default(0.00);
                $table->float('remaining_amount')->default(0.00);
                $table->dateTime('paid_date');
                $table->tinyInteger('status')->default(1);
                $table->timestamps();
            });
        }

        if(!Schema::hasTable('user_billing_infos')) {

            Schema::create('user_billing_infos', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('user_id');
                $table->string('account_name')->nullable();
                $table->string('paypal_email')->nullable();
                $table->string('account_no')->nullable();
                $table->string('route_no')->nullable();
                $table->tinyInteger('status')->default(1);
                $table->timestamps();
            });
        }

        if(!Schema::hasTable('wishlists')) {

            Schema::create('wishlists', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name')->default("DEFAULT");
                $table->integer('host_id');
                $table->integer('user_id');
                $table->tinyInteger('status')->default(1);
                $table->timestamps();
            });
        }

        // Admins
        if(!Schema::hasTable('admins')) {

            Schema::create('admins', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->string('email')->unique();
                $table->string('about')->default("");
                $table->string('mobile')->default("");
                $table->string('picture')->default(envfile('APP_URL')."/placeholder.jpg");
                $table->string('password');
                $table->string('timezone')->default('America/Los_Angeles');
                $table->tinyInteger('status')->default(1);
                $table->rememberToken();
                $table->timestamps();
            });
        
        }

        // Providers
        if(!Schema::hasTable('providers')) {
            
            Schema::create('providers', function (Blueprint $table) {
                $table->increments('id');
                $table->string('unique_id');
                $table->string('username');
                $table->string('first_name');
                $table->string('last_name');
                $table->string('name');
                $table->string('email')->unique();
                $table->string('token');
                $table->string('provider_type')->default(0);
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password');
                $table->text('description')->nullable();
                $table->string('mobile')->default("");
                $table->string('picture')->default(envfile('APP_URL')."/placeholder.jpg");
                $table->string('token_expiry');
                $table->integer('language_id')->default(0);
                $table->string('work')->default("");
                $table->string('school')->default("");
                $table->text('languages')->default("");
                $table->string('response_rate')->default("");
                $table->string('device_token')->default('');
                $table->enum('device_type',array('web','android','ios'));
                $table->enum('register_type',array('web','android','ios'));
                $table->enum('login_by',array('manual','facebook','google','twitter' , 'linkedin'));
                $table->string('social_unique_id')->default('');
                $table->enum('gender',array('male','female','others'));
                $table->double('latitude',15,8);
                $table->double('longitude',15,8);
                $table->text('full_address')->nullable();
                $table->string('street_details')->default("");
                $table->string('city')->default("");
                $table->string('state')->default("");
                $table->string('zipcode')->default("");
                $table->string('payment_mode');
                $table->string('provider_card_id')->default(0);
                $table->string('timezone')->default('America/Los_Angeles');
                $table->tinyInteger('registration_steps')->default(0);
                $table->integer('push_notification_status')->default(1);
                $table->integer('email_notification_status')->default(1);
                $table->integer('is_verified')->default(0);
                $table->string('verification_code')->default('');
                $table->string('verification_code_expiry')->default('');
                $table->tinyInteger('status')->default(0);
                $table->timestamps();
            
            });
        }

        if(!Schema::hasTable('provider_cards')) {

            Schema::create('provider_cards', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('provider_id');
                $table->string('card_name');
                $table->string('customer_id');
                $table->string('last_four');
                $table->string('card_token');
                $table->string('is_default')->default(0);
                $table->tinyInteger('status')->default(1);
                $table->timestamps();
            });

        }

        if(!Schema::hasTable('provider_redeems')) {
            
            Schema::create('provider_redeems', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('provider_id');
                $table->float('total')->default(0.00);
                $table->float('paid_amount')->default(0.00);
                $table->float('remaining_amount')->default(0.00);
                $table->float('dispute_amount')->default(0.00);
                $table->dateTime('paid_date');
                $table->tinyInteger('status')->default(1);
                $table->timestamps();
            });
        
        }

        if(!Schema::hasTable('provider_billing_infos')) {

            Schema::create('provider_billing_infos', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('provider_id');
                $table->string('account_name')->nullable();
                $table->string('paypal_email')->nullable();
                $table->string('account_no')->nullable();
                $table->string('route_no')->nullable();
                $table->tinyInteger('status')->default(1);
                $table->timestamps();
            });
        
        }

        if(!Schema::hasTable('provider_details')) {

            Schema::create('provider_details', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('provider_id');
                $table->tinyInteger('status')->default(1);
                $table->timestamps();
            });
        
        }

        if(!Schema::hasTable('documents')) {

            Schema::create('documents', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->string('description');
                $table->tinyInteger('status')->default(1);
                $table->timestamps();
            });
        
        }

        if(!Schema::hasTable('provider_documents')) {

            Schema::create('provider_documents', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('provider_id');
                $table->integer('document_id');
                $table->string('document_url');
                $table->tinyInteger('status')->default(1);
                $table->timestamps();
            });
        
        }

        if(!Schema::hasTable('provider_subscriptions')) {

            Schema::create('provider_subscriptions', function (Blueprint $table) {
                $table->increments('id');
                $table->string('unique_id');
                $table->string('title');
                $table->text('description');
                $table->string('picture');
                $table->string('amount');
                $table->string('plan');
                $table->enum('plan_type', [PLAN_TYPE_MONTH, PLAN_TYPE_DAY, PLAN_TYPE_YEAR])->default(PLAN_TYPE_MONTH);
                $table->integer('total_subscribers')->default(0);
                $table->tinyInteger('status')->default(APPROVED);
                $table->tinyInteger('is_popular')->default(NO);
                $table->tinyInteger('is_free_subscription')->default(NO);
                $table->timestamps();
            });
            
        }

        if(!Schema::hasTable('provider_subscription_payments')) {

            Schema::create('provider_subscription_payments', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('provider_id');
                $table->integer('provider_subscription_id');
                $table->string('payment_id');
                $table->string('payment_mode')->default(CARD);
                $table->dateTime('expiry_date');
                $table->float('subscription_amount')->default(0.00);
                $table->float('paid_amount')->default(0.00);
                $table->dateTime('paid_date')->nullable();
                $table->tinyInteger('status');
                $table->integer('is_current_subscription')->default(NO);
                $table->integer('is_cancelled')->default(NO);
                $table->string('cancelled_reason')->default("");
                $table->string('subscribed_by')->default(PROVIDER);
                $table->timestamps();
            });
            
        }

        if (!Schema::hasTable('bell_notifications')) {
            
            Schema::create('bell_notifications', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('from_id');
                $table->integer('to_id');
                $table->string('notification_type');
                $table->string('redirection_type');
                $table->enum('receiver', ['user','provider','others'])->default('user');
                $table->text('message');
                $table->integer('booking_id')->default(0);
                $table->integer('host_id')->default(0);
                $table->integer('status')->default(BELL_NOTIFICATION_STATUS_UNREAD);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('user_vehicles')) {

            Schema::create('user_vehicles', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('user_id');
                $table->string('vehicle_type');
                $table->string('vehicle_number');
                $table->string('vehicle_brand');
                $table->string('vehicle_model');
                $table->tinyInteger('status')->default(1);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('user_cards');
        Schema::dropIfExists('user_refunds');
        Schema::dropIfExists('user_billing_infos');
        Schema::dropIfExists('wishlists');
        Schema::dropIfExists('admins');
        Schema::dropIfExists('providers');
        Schema::dropIfExists('provider_cards');
        Schema::dropIfExists('provider_redeems');
        Schema::dropIfExists('provider_billing_infos');
        Schema::dropIfExists('provider_details');
        Schema::dropIfExists('documents');
        Schema::dropIfExists('provider_documents');
        Schema::dropIfExists('provider_subscriptions');
        Schema::dropIfExists('provider_subscription_payments');
        Schema::dropIfExists('bell_notifications');
        Schema::dropIfExists('user_vehicles');
    }
}
