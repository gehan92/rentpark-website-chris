<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddV1BookingsRelatedMigrations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('bookings')) {

            Schema::create('bookings', function (Blueprint $table) {
                $table->increments('id');
                $table->string('unique_id');
                $table->integer('user_id');
                $table->integer('provider_id');
                $table->integer('host_id');
                $table->text('description');
                $table->integer('user_vehicle_id');
                $table->string('duration');
                $table->float('per_day')->default(0.00);
                $table->float('per_hour')->default(0.00);
                $table->float('per_week')->default(0.00);
                $table->float('per_month')->default(0.00);
                $table->dateTime('checkin');
                $table->dateTime('checkout');
                $table->dateTime('actual_checkin')->nullable();
                $table->dateTime('actual_checkout')->nullable();
                $table->float('total_days')->default(0);
                $table->string('currency')->default("$");
                $table->float('total')->default(0.00);
                $table->string('payment_mode')->default("");
                $table->tinyInteger('status')->default(0);
                $table->text('cancelled_reason');
                $table->dateTime('cancelled_date');
                $table->tinyInteger('is_rebooking')->default(0);
                $table->timestamps();
            });

        }

        // Not used
        if (!Schema::hasTable('booking_chats')) {

            Schema::create('booking_chats', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('booking_id');
                $table->integer('user_id');
                $table->integer('provider_id');
                $table->integer('host_id');
                $table->string('type');
                $table->text('message');
                $table->tinyInteger('is_delivered');
                $table->tinyInteger('status')->default(1);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('booking_payments')) {

            Schema::create('booking_payments', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('booking_id');
                $table->integer('user_id');
                $table->integer('provider_id');
                $table->integer('host_id');
                $table->string('payment_id');
                $table->string('payment_mode')->default('cod');
                $table->string('currency')->default("$");
                $table->string('total_time')->default(0);
                $table->float('base_price')->default(0.00);
                $table->float('per_hour')->default(0.00);
                $table->float('per_day')->default(0.00);
                $table->float('per_week')->default(0.00);
                $table->float('per_month')->default(0.00);
                $table->float('cleaning_fee')->default(0.00);
                $table->float('time_price')->default(0.00);
                $table->float('other_price')->default(0.00);
                $table->float('sub_total')->default(0.00);
                $table->float('tax_price')->default(0.00);
                $table->float('actual_total')->default(0.00);
                $table->float('total')->default(0.00);
                $table->float('paid_amount')->default(0.00);
                $table->dateTime('paid_date')->nullable();
                $table->float('admin_amount')->default(0.00);
                $table->float('provider_amount')->default(0.00);
                $table->tinyInteger('status')->default(1);
                $table->timestamps();
            });

        }

        if (!Schema::hasTable('booking_user_reviews')) {

            Schema::create('booking_user_reviews', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('booking_id');
                $table->integer('user_id');
                $table->integer('provider_id');
                $table->integer('host_id');
                $table->string('ratings');
                $table->string('review');
                $table->tinyInteger('status')->default(1);
                $table->timestamps();
            
            });

        }

        if (!Schema::hasTable('booking_provider_reviews')) {

            Schema::create('booking_provider_reviews', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('booking_id');            
                $table->integer('user_id');
                $table->integer('provider_id');
                $table->integer('host_id');
                $table->string('ratings');
                $table->string('review');
                $table->tinyInteger('status')->default(1);
                $table->timestamps();
            });

        }

        if (!Schema::hasTable('chat_messages')) {

            Schema::create('chat_messages', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('booking_id')->default(0);
                $table->integer('host_id')->default(0);
                $table->integer('user_id');
                $table->integer('provider_id');
                $table->string('type');
                $table->text('message');
                $table->tinyInteger('is_delivered');
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
        Schema::dropIfExists('bookings');
        Schema::dropIfExists('booking_chats');
        Schema::dropIfExists('booking_payments');
        Schema::dropIfExists('booking_user_reviews');
        Schema::dropIfExists('booking_provider_reviews');
        Schema::dropIfExists('chat_messages');
    }
}
