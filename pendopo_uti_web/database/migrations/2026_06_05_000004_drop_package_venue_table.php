<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Single-venue: relasi many-to-many paket<->venue tidak lagi diperlukan
 * (semua paket berlaku untuk satu-satunya venue). Tabel pivot di-drop.
 * down() membuatnya kembali apabila perlu dikembalikan ke skema multi-venue.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('package_venue');
    }

    public function down(): void
    {
        Schema::create('package_venue', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_id')->constrained()->cascadeOnDelete();
            $table->foreignId('venue_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['package_id', 'venue_id']);
        });
    }
};
