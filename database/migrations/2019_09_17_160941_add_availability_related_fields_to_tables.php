<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAvailabilityRelatedFieldsToTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('host_availabilities', function (Blueprint $table) {
            $table->date('date')->before('status'); 
            $table->time('time')->after('date'); 
            $table->integer('slot')->after('time'); 
            $table->float('total_spaces')->default(1)->after('slot');
            $table->float('used_spaces')->default(1)->after('total_spaces');
            $table->float('remaining_spaces')->default(1)->after('used_spaces');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('host_availabilities', function (Blueprint $table) {
            $table->dropColumn('date'); 
            $table->dropColumn('time'); 
            $table->dropColumn('slot'); 
            $table->dropColumn('total_spaces');
            $table->dropColumn('used_spaces');
            $table->dropColumn('remaining_spaces');
        });
    }
}
