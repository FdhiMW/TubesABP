<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * TAHAP 2 — Hapus kolom yang tidak terpakai.
 *
 * - bookings.payment_reference : hanya pernah ditulis sekali oleh fungsi
 *   pembayaran yang tidak pernah jalan, dan tidak pernah dibaca di mana pun.
 * - venues.photos : tidak pernah ditampilkan di web maupun Flutter; hanya
 *   dimasukkan ke prompt AI, tanpa mekanisme upload. (Saat fitur galeri
 *   foto dibutuhkan, rollback migration ini.)
 *
 * Reversible: down() menambahkan kembali kedua kolom dengan tipe aslinya.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (Schema::hasColumn('bookings', 'payment_reference')) {
                $table->dropColumn('payment_reference');
            }
        });

        Schema::table('venues', function (Blueprint $table) {
            if (Schema::hasColumn('venues', 'photos')) {
                $table->dropColumn('photos');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (! Schema::hasColumn('bookings', 'payment_reference')) {
                $table->string('payment_reference')->nullable()->after('midtrans_order_id');
            }
        });

        Schema::table('venues', function (Blueprint $table) {
            if (! Schema::hasColumn('venues', 'photos')) {
                $table->json('photos')->nullable()->after('facilities');
            }
        });
    }
};