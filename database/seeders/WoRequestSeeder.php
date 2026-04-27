<?php

namespace Database\Seeders;

use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WoRequestSeeder extends Seeder
{
    public function run(): void
    {
        // Mengambil booking yang sudah di confirmed biar dipasangkan dengan WO request
        $confirmedBooking = Booking::where('status', 'confirmed')->first();
        $awaitingBooking  = Booking::where('status', 'awaiting_payment')->first();

        if (! $confirmedBooking || ! $awaitingBooking) {
            $this->command->warn('Skip WoRequestSeeder — jalankan BookingSeeder dulu.');
            return;
        }

        $requests = [
            [
                'user_id'          => $confirmedBooking->user_id,
                'booking_id'       => $confirmedBooking->id,
                'package_name'     => 'Paket Wedding Premium',
                'request_details'  => 'Butuh paket lengkap WO untuk pernikahan: dekorasi pelaminan, MC, '
                                    . 'catering 500 orang, dokumentasi foto & video, hiburan band akustik.',
                'estimated_budget' => 75000000,
                'status'           => 'approved',
                'admin_notes'      => 'Disetujui. Tim WO akan menghubungi user dalam 2 hari kerja.',
                'created_at'       => now(),
                'updated_at'       => now(),
            ],
            [
                'user_id'          => $awaitingBooking->user_id,
                'booking_id'       => $awaitingBooking->id,
                'package_name'     => 'Paket Wedding Standar',
                'request_details'  => 'Butuh dekorasi sederhana, MC, dan catering 400 orang.',
                'estimated_budget' => 45000000,
                'status'           => 'requested',
                'created_at'       => now(),
                'updated_at'       => now(),
            ],
        ];

        foreach ($requests as $request) {
            DB::table('wo_requests')->insert($request);
        }
    }
}