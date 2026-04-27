<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,         // 1 admin
            UserSeeder::class,          // 3 user biasa
            VenueSeeder::class,         // 1 venue
            VenueScheduleSeeder::class, // 2 jadwal block (depend on venue)
            BookingSeeder::class,       // 5 booking (depend on user + venue)
            SurveySeeder::class,        // 4 survey (depend on user + venue)
            WoRequestSeeder::class,     // 2 WO (depend on booking)
            NotificationSeeder::class,  // 5 notifikasi (depend on user + booking + survey)
        ]);
    }
}