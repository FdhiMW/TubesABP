<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Survey;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        $fadhi = User::where('email', 'fadhi@gmail.com')->first();
        $davino = User::where('email', 'davino@gmail.com')->first();
        $nayubi = User::where('email', 'nayubi@gmail.com')->first();

        if (! $fadhi || ! $davino || ! $nayubi) {
            $this->command->warn('Skip NotificationSeeder — jalankan UserSeeder dulu.');
            return;
        }

        $confirmedBooking = Booking::where('status', 'confirmed')->first();
        $awaitingBooking  = Booking::where('status', 'awaiting_payment')->first();
        $confirmedSurvey  = Survey::where('status', 'confirmed')->first();

        $notifications = [
            [
                'user_id'        => $fadhi->id,
                'title'          => 'Booking Anda Dikonfirmasi',
                'message'        => 'Booking dengan kode BOOK-DUMMY004 telah dikonfirmasi. Terima kasih!',
                'type'           => 'success',
                'reference_type' => $confirmedBooking ? 'booking' : null,
                'reference_id'   => $confirmedBooking?->id,
                'is_read'        => true,
                'created_at'     => Carbon::now()->subDays(2),
                'updated_at'     => Carbon::now()->subDays(2),
            ],
            [
                'user_id'        => $nayubi->id,
                'title'          => 'Pembayaran Menunggu',
                'message'        => 'Booking Anda telah disetujui. Silakan lakukan pembayaran melalui Midtrans.',
                'type'           => 'info',
                'reference_type' => $awaitingBooking ? 'booking' : null,
                'reference_id'   => $awaitingBooking?->id,
                'is_read'        => false,
                'created_at'     => Carbon::now()->subDay(),
                'updated_at'     => Carbon::now()->subDay(),
            ],
            [
                'user_id'        => $nayubi->id,
                'title'          => 'Jadwal Survey Dikonfirmasi',
                'message'        => 'Jadwal survey Anda telah disetujui. Silakan datang sesuai jadwal.',
                'type'           => 'success',
                'reference_type' => $confirmedSurvey ? 'survey' : null,
                'reference_id'   => $confirmedSurvey?->id,
                'is_read'        => false,
                'created_at'     => Carbon::now()->subHours(5),
                'updated_at'     => Carbon::now()->subHours(5),
            ],
            [
                'user_id'    => $davino->id,
                'title'      => 'Booking Dibatalkan',
                'message'    => 'Booking BOOK-DUMMY005 telah dibatalkan sesuai permintaan Anda.',
                'type'       => 'warning',
                'is_read'    => true,
                'created_at' => Carbon::now()->subDays(15),
                'updated_at' => Carbon::now()->subDays(15),
            ],
            [
                'user_id'    => $fadhi->id,
                'title'      => 'Selamat Datang di Pendopo UTI',
                'message'    => 'Terima kasih telah mendaftar. Mulai booking venue impian Anda sekarang!',
                'type'       => 'info',
                'is_read'    => false,
                'created_at' => Carbon::now()->subDays(30),
                'updated_at' => Carbon::now()->subDays(30),
            ],
        ];

        foreach ($notifications as $notification) {
            DB::table('notifications')->insert($notification);
        }
    }
}