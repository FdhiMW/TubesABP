<?php

namespace Database\Seeders;

use App\Models\Survey;
use App\Models\User;
use App\Models\Venue;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SurveySeeder extends Seeder
{
    public function run(): void
    {
        $venue = Venue::first();
        $fadhi = User::where('email', 'fadhi@gmail.com')->first();
        $davino = User::where('email', 'davino@gmail.com')->first();
        $nayubi = User::where('email', 'nayubi@gmail.com')->first();

        if (! $venue || ! $fadhi || ! $davino || ! $nayubi) {
            $this->command->warn('Skip SurveySeeder — jalankan UserSeeder & VenueSeeder dulu.');
            return;
        }

        $surveys = [
            // 1. Survey PENDING — menunggu konfirmasi admin
            [
                'user_id'       => $fadhi->id,
                'venue_id'      => $venue->id,
                'proposed_date' => Carbon::now()->addDays(7)->toDateString(),
                'proposed_time' => '10:00:00',
                'end_time'      => '11:00:00',
                'status'        => 'pending',
                'notes'         => 'Ingin melihat ruang utama dan area parkir untuk acara pernikahan.',
            ],

            // 2. Survey PENDING dari user lain
            [
                'user_id'       => $davino->id,
                'venue_id'      => $venue->id,
                'proposed_date' => Carbon::now()->addDays(10)->toDateString(),
                'proposed_time' => '14:00:00',
                'end_time'      => '15:00:00',
                'status'        => 'pending',
                'notes'         => 'Mau cek lokasi untuk acara gathering perusahaan.',
            ],

            // 3. Survey CONFIRMED — sudah disetujui admin
            [
                'user_id'        => $nayubi->id,
                'venue_id'       => $venue->id,
                'proposed_date'  => Carbon::now()->addDays(5)->toDateString(),
                'proposed_time'  => '13:00:00',
                'end_time'       => '14:00:00',
                'confirmed_date' => Carbon::now()->addDays(5)->toDateString(),
                'confirmed_time' => '13:00:00',
                'status'         => 'confirmed',
                'notes'          => 'Survey untuk persiapan acara resepsi pernikahan.',
                'admin_notes'    => 'Disetujui — silakan datang sesuai jadwal. Akan ditemani staff venue.',
            ],

            // 4. Survey COMPLETED — sudah selesai dilaksanakan
            [
                'user_id'        => $fadhi->id,
                'venue_id'       => $venue->id,
                'proposed_date'  => Carbon::now()->subDays(7)->toDateString(),
                'proposed_time'  => '11:00:00',
                'end_time'       => '12:00:00',
                'confirmed_date' => Carbon::now()->subDays(7)->toDateString(),
                'confirmed_time' => '11:00:00',
                'status'         => 'completed',
                'notes'          => 'Survey awal sebelum booking.',
                'admin_notes'    => 'Survey selesai dilaksanakan. User puas dengan fasilitas.',
            ],
        ];

        foreach ($surveys as $survey) {
            Survey::create($survey);
        }
    }
}