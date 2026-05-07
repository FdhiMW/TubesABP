<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Hapus kolom enum lama
            if (Schema::hasColumn('bookings', 'package')) {
                $table->dropColumn('package');
            }
        });

        Schema::table('bookings', function (Blueprint $table) {
            // Nambah foreign key ke packages
            $table->foreignId('package_id')
                  ->nullable()
                  ->after('guest_count')
                  ->constrained('packages')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['package_id']);
            $table->dropColumn('package_id');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->enum('package', ['basic', 'premium', 'luxury'])
                  ->default('basic')
                  ->after('guest_count');
        });
    }
};