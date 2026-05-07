<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            UserSeeder::class,
            VenueSeeder::class,
            PackageSeeder::class,
            VenueScheduleSeeder::class,
            BookingSeeder::class,
            SurveySeeder::class,
            WoRequestSeeder::class,
            NotificationSeeder::class,
        ]);
    }
}