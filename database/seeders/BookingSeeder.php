<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\User;
use App\Models\Venue;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class BookingSeeder extends Seeder
{
    public function run(): void
    {
        $venue = Venue::first();
        $fadhi = User::where('email', 'fadhi@gmail.com')->first();
        $davino = User::where('email', 'davino@gmail.com')->first();
        $nayubi = User::where('email', 'nayubi@gmail.com')->first();

        if (! $venue || ! $fadhi || ! $davino || ! $nayubi) {
            $this->command->warn('Skip BookingSeeder — jalankan UserSeeder & VenueSeeder dulu.');
            return;
        }

        $bookings = [
            // 1. Booking PENDING — menunggu approval admin
            [
                'booking_code' => 'BOOK-DUMMY001',
                'user_id'      => $fadhi->id,
                'venue_id'     => $venue->id,
                'event_date'   => Carbon::now()->addDays(20)->toDateString(),
                'end_date'     => Carbon::now()->addDays(20)->toDateString(),
                'event_time'   => '09:00:00',
                'end_time'     => '17:00:00',
                'guest_count'  => 300,
                'total_price'  => 25000000,
                'status'       => 'pending',
            ],

            // 2. Booking PENDING dari user lain di tanggal berbeda
            [
                'booking_code' => 'BOOK-DUMMY002',
                'user_id'      => $davino->id,
                'venue_id'     => $venue->id,
                'event_date'   => Carbon::now()->addDays(35)->toDateString(),
                'end_date'     => Carbon::now()->addDays(35)->toDateString(),
                'event_time'   => '10:00:00',
                'end_time'     => '20:00:00',
                'guest_count'  => 250,
                'total_price'  => 25000000,
                'status'       => 'pending',
            ],

            // 3. Booking AWAITING_PAYMENT — sudah di-approve, menunggu pembayaran
            [
                'booking_code' => 'BOOK-DUMMY003',
                'user_id'      => $nayubi->id,
                'venue_id'     => $venue->id,
                'event_date'   => Carbon::now()->addDays(50)->toDateString(),
                'end_date'     => Carbon::now()->addDays(50)->toDateString(),
                'event_time'   => '11:00:00',
                'end_time'     => '21:00:00',
                'guest_count'  => 400,
                'total_price'  => 25000000,
                'status'       => 'awaiting_payment',
            ],

            // 4. Booking CONFIRMED — sudah lunas & dikonfirmasi
            [
                'booking_code'      => 'BOOK-DUMMY004',
                'user_id'           => $fadhi->id,
                'venue_id'          => $venue->id,
                'event_date'        => Carbon::now()->addDays(60)->toDateString(),
                'end_date'          => Carbon::now()->addDays(60)->toDateString(),
                'event_time'        => '08:00:00',
                'end_time'          => '22:00:00',
                'guest_count'       => 500,
                'total_price'       => 25000000,
                'status'            => 'confirmed',
                'payment_reference' => 'MIDTRANS-DUMMY-12345',
            ],

            // 5. Booking CANCELLED — pernah dibatalkan
            [
                'booking_code'        => 'BOOK-DUMMY005',
                'user_id'             => $davino->id,
                'venue_id'            => $venue->id,
                'event_date'          => Carbon::now()->subDays(10)->toDateString(),
                'end_date'            => Carbon::now()->subDays(10)->toDateString(),
                'event_time'          => '12:00:00',
                'end_time'            => '18:00:00',
                'guest_count'         => 150,
                'total_price'         => 25000000,
                'status'              => 'cancelled',
                'cancellation_reason' => 'User membatalkan karena perubahan jadwal acara.',
                'cancelled_at'        => Carbon::now()->subDays(15),
            ],
        ];

        foreach ($bookings as $booking) {
            Booking::updateOrCreate(
                ['booking_code' => $booking['booking_code']],
                $booking
            );
        }
    }
}