<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(DemoDataSeeder::class);
        $this->call(SettingsSeeder::class);
        $this->call(V1Seeder::class);
        // V2Seeder
        // V3Seeder
    }
}
