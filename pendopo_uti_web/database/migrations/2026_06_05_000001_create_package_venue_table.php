<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * TAHAP 1 — Relasi packages <-> venues (many-to-many).
 *
 * Sistem direncanakan multi-venue, sehingga satu paket (mis. "Premium")
 * bisa ditawarkan di beberapa venue, dan satu venue menawarkan banyak paket.
 * Relasi yang tepat untuk kasus ini adalah many-to-many lewat tabel pivot,
 * BUKAN one-to-many (yang akan memaksa duplikasi paket per venue).
 *
 * Catatan: kolom bookings.package_id dan bookings.venue_id TETAP. Pivot ini
 * hanya menyatakan "paket apa saja yang tersedia di venue mana".
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('package_venue', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_id')->constrained()->cascadeOnDelete();
            $table->foreignId('venue_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            // Satu paket tidak boleh terdaftar dua kali di venue yang sama.
            $table->unique(['package_id', 'venue_id']);
            $table->index('venue_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('package_venue');
    }
};