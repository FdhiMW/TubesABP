<?php

namespace Database\Seeders;

use App\Models\Venue;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VenueScheduleSeeder extends Seeder
{
    public function run(): void
    {
        $venue = Venue::first();

        if (! $venue) {
            $this->command->warn('Skip VenueScheduleSeeder — jalankan VenueSeeder dulu.');
            return;
        }

        $schedules = [
            [
                'venue_id'     => $venue->id,
                'blocked_date' => Carbon::now()->addDays(15)->toDateString(),
                'block_type'   => 'maintenance',
                'reason'       => 'Perawatan rutin AC dan sound system.',
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'venue_id'     => $venue->id,
                'blocked_date' => Carbon::now()->addDays(25)->toDateString(),
                'block_type'   => 'manual',
                'reason'       => 'Libur nasional — venue tutup.',
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
        ];

        foreach ($schedules as $schedule) {
            DB::table('venue_schedules')->updateOrInsert(
                [
                    'venue_id'     => $schedule['venue_id'],
                    'blocked_date' => $schedule['blocked_date'],
                ],
                $schedule
            );
        }
    }
}