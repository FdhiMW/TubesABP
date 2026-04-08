<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('venues', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('location');
            $table->integer('capacity')->unsigned();
            $table->decimal('price_per_day', 15, 2);
            $table->json('facilities')->nullable();      // FR-003: fasilitas venue
            $table->json('photos')->nullable();           // FR-003: foto venue
            $table->enum('status', ['active', 'inactive', 'maintenance'])->default('active');
            $table->timestamps();

            $table->index('status');
            $table->index('capacity');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('venues');
    }
};