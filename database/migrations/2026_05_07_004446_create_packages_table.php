<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');                         // "Basic Package"
            $table->string('slug')->unique();               // "basic-package"
            $table->decimal('price', 15, 2);                // 25000000
            $table->string('price_label', 50);              // "Rp 25jt"
            $table->string('tagline')->nullable();          // "Untuk acara sederhana uhuy"
            $table->json('features');                       // ["Dekorasi dasar", "item2 lain"]
            $table->boolean('is_popular')->default(false);  // Badge "Popular"
            $table->boolean('is_active')->default(false);   // Active = visible to user
            $table->string('color', 20)->default('#c9a861'); // Hex color
            $table->integer('sort_order')->default(0);      // Urutan tampil
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};