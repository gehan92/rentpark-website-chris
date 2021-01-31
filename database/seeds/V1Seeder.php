<?php

use Illuminate\Database\Seeder;

class V1Seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		if(Schema::hasTable('lookups')) {

	        DB::table('lookups')->insert([
	    		[
			        'type' => 'host_type',
			        'key' => 'driveway',
			        'value' => 'Driveway',
			        'picture' => "",
			        'is_amenity' => 0,
			        'status' => 1
			    ],
			    [
			        'type' => 'host_type',
			        'key' => 'garage',
			        'value' => 'Garage',
			        'picture' => "",
			        'is_amenity' => 0,
			        'status' => 1
			    ],
			    [
			        'type' => 'host_type',
			        'key' => 'carpark',
			        'value' => 'CarPark',
			        'picture' => "",
			        'is_amenity' => 0,
			        'status' => 1

			    ],
			    // Used for Host Management.Dont change the Key and value
			    [
			        'type' => 'host_owner_type',
			        'key' => 'owner',
			        'value' => 'Owner',
			        'picture' => "",
			        'is_amenity' => 0,
			        'status' => 1
			    ],
			    [
			        'type' => 'host_owner_type',
			        'key' => 'business',
			        'value' => 'Business / Organization',
			        'picture' => "",
			        'is_amenity' => 0,
			        'status' => 1
			    ],
			    [
			        'type' => 'driveway',
			        'picture' => envfile('APP_URL')."/images/cctv.png",
			        'key' => 'cctv',
			        'value' => 'CCTV',
			        'is_amenity' => 0,
			        'status' => 1
			    ],
			    [
			        'type' => 'driveway',
			        'picture' => envfile('APP_URL')."/images/plug.png",
			        'key' => 'electric-charging',
			        'value' => 'Electric Charging',
			        'is_amenity' => 0,
			        'status' => 1
			    ],
			    [
			        'type' => 'garage',
			        'picture' => envfile('APP_URL')."/images/plug.png",
			        'key' => 'electric-charging',
			        'value' => 'Electric Charging',
			        'is_amenity' => 0,
			        'status' => 1
			    ],
			    [
			        'type' => 'garage',
			        'picture' => envfile('APP_URL')."/images/cctv.png",
			        'key' => 'cctv',
			        'value' => 'CCTV',
			        'is_amenity' => 0,
			        'status' => 1
			    ],
			    [
			        'type' => 'garage',
			        'picture' => envfile('APP_URL')."/images/mutiple-entry.png",
			        'key' => 'multiple-entry-exit',
			        'value' => 'Multiple Entry/ Exit',
			        'is_amenity' => 0,
			        'status' => 1
			    ],

			    [
			        'type' => 'carpark',
			        'picture' => envfile('APP_URL')."/images/plug.png",
			        'key' => 'electric-charging',
			        'value' => 'Electric Charging',
			        'is_amenity' => 0,
			        'status' => 1
			    ],
			    [
			        'type' => 'carpark',
			        'picture' => envfile('APP_URL')."/images/cctv.png",
			        'key' => 'cctv',
			        'value' => 'CCTV',
			        'is_amenity' => 0,
			        'status' => 1
			    ],
			    [
			        'type' => 'carpark',
			        'picture' => envfile('APP_URL')."/images/mutiple-entry.png",
			        'key' => 'multiple-entry-exit',
			        'value' => 'Multiple Entry/ Exit',
			        'picture' => "",
			        'is_amenity' => 0,
			        'status' => 1
			    ],
			    [
			        'type' => 'carpark',
			        'picture' => envfile('APP_URL')."/images/police.png",
			        'key' => 'covered',
			        'value' => 'Covered',
			        'picture' => "",
			        'is_amenity' => 0,
			        'status' => 1
			    ],
			    [
			        'type' => 'carpark',
			        'picture' => envfile('APP_URL')."/images/police.png",
			        'key' => 'onsite-staff',
			        'value' => 'OnSite Staff',
			        'picture' => "",
			        'is_amenity' => 0,
			        'status' => 1
			    ],
			    [
			        'type' => 'carpark',
			        'picture' => envfile('APP_URL')."/images/police.png",
			        'key' => 'disabled-access',
			        'value' => 'Disabled Access',
			        'picture' => "",
			        'is_amenity' => 0,
			        'status' => 1
			    ],
			
			]);
	    }

		if(Schema::hasTable('mobile_registers')) {

		    DB::table('mobile_registers')->insert([
	    		[
			        'type' => 'android',
			        'count' => 0,
			        'user_type' => USER
			    ],
			    [
			        'type' => 'ios',
			        'count' => 0,
			        'user_type' => USER
			    ],
			    [
			        'type' => 'web',
			        'count' => 2,
			        'user_type' => USER
			    ]
			]);

		}

	}
}
