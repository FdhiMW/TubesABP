<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
        $table->id();
        $table->string('booking_code')->unique();
        $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Asumsi tabel users bawaan Laravel sudah ada
        $table->foreignId('venue_id')->constrained()->onDelete('cascade');
        $table->date('start_date');
        $table->date('end_date');
        $table->decimal('total_price', 15, 2);
        $table->enum('status', ['pending', 'paid', 'confirmed', 'cancelled', 'completed'])->default('pending');
        $table->text('cancellation_reason')->nullable();
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
