<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddV1SettingsRelatedTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('static_pages')) {

            Schema::create('static_pages', function (Blueprint $table) {
                $table->increments('id');
                $table->string('unique_id')->default(uniqid());
                $table->string('title')->unique();
                $table->text('description');
                $table->enum('type',['about','privacy','terms','refund','cancellation','faq','help','contact','others']);
                $table->tinyInteger('status')->default(APPROVED);
                $table->string('section_type')->nullable();
                $table->softDeletes();
                $table->timestamps();
            });

        }

        if (!Schema::hasTable('page_counters')) {

            Schema::create('page_counters', function (Blueprint $table) {
                $table->increments('id');
                $table->string('unique_id');
                $table->string('page');
                $table->integer('count');
                $table->softDeletes();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('mobile_registers')) {

            Schema::create('mobile_registers', function (Blueprint $table) {
                $table->increments('id');
                $table->string('type');
                $table->integer('count');
                $table->string('user_type');
                $table->timestamps();
            });

        }
        
        if (!Schema::hasTable('lookups')) {

            Schema::create('lookups', function (Blueprint $table) {
                $table->increments('id');
                $table->string('type');
                $table->string('picture')->nullable();
                $table->string('key');
                $table->string('value');
                $table->tinyInteger('status')->default(1);
                $table->timestamps();
            });

        }

        if (!Schema::hasTable('bell_notification_templates')) {

            Schema::create('bell_notification_templates', function (Blueprint $table) {
                $table->increments('id');
                $table->string('unique_id');
                $table->string('type');
                $table->string('title');
                $table->text('message');
                $table->integer('status')->default(APPROVED);
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
        Schema::dropIfExists('static_pages');
        Schema::dropIfExists('page_counters');
        Schema::dropIfExists('mobile_registers');
        Schema::dropIfExists('lookups');
        Schema::dropIfExists('bell_notification_templates');
    }
}
