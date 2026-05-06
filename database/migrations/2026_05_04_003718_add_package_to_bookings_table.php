<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // end_time mungkin sudah ada, jadi cek dulu
            if (! Schema::hasColumn('bookings', 'end_time')) {
                $table->time('end_time')->nullable()->after('event_time');
            }

            // Kolom package — wajib untuk fitur ini
            if (! Schema::hasColumn('bookings', 'package')) {
                $table->enum('package', ['basic', 'premium', 'luxury'])
                      ->default('basic')
                      ->after('guest_count');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (Schema::hasColumn('bookings', 'package')) {
                $table->dropColumn('package');
            }
        });
    }
};