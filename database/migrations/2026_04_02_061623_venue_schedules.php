<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('venue_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venue_id')->constrained()->onDelete('cascade');
            $table->date('blocked_date');
            $table->enum('block_type', ['booking', 'maintenance', 'manual'])->default('manual');
            $table->string('reason')->nullable();
            $table->timestamps();

            // Satu venue tidak boleh double-block di tanggal yang sama
            $table->unique(['venue_id', 'blocked_date']);
            $table->index('blocked_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('venue_schedules');
    }
};