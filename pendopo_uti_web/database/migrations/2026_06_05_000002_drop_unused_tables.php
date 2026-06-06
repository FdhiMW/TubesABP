<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * TAHAP 2 — Hapus tabel yang tidak terpakai.
 *
 * venue_schedules, wo_requests, dan notifications punya migration + seeder,
 * tetapi NOL pemakaian di model/controller/route/view maupun aplikasi Flutter.
 *
 * Migration ini reversible: method down() membangun ulang ketiga tabel persis
 * seperti definisi aslinya, sehingga cukup `php artisan migrate:rollback`
 * saat fiturnya nanti dibutuhkan.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('venue_schedules');
        Schema::dropIfExists('wo_requests');
    }

    public function down(): void
    {
        Schema::create('venue_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venue_id')->constrained()->onDelete('cascade');
            $table->date('blocked_date');
            $table->enum('block_type', ['booking', 'maintenance', 'manual'])->default('manual');
            $table->string('reason')->nullable();
            $table->timestamps();

            $table->unique(['venue_id', 'blocked_date']);
            $table->index('blocked_date');
        });

        Schema::create('wo_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            $table->string('package_name')->nullable();
            $table->text('request_details');
            $table->decimal('estimated_budget', 15, 2)->nullable();
            $table->enum('status', ['requested', 'reviewed', 'approved', 'rejected'])->default('requested');
            $table->text('admin_notes')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index('booking_id');
        });

        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('message');
            $table->string('type', 50)->default('info');
            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamps();

            $table->index(['user_id', 'is_read']);
            $table->index(['reference_type', 'reference_id']);
        });
    }
};