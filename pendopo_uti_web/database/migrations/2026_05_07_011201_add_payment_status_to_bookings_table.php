<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (! Schema::hasColumn('bookings', 'payment_status')) {
                $table->enum('payment_status', [
                    'unpaid',
                    'pending',
                    'paid',
                    'failed',
                ])->default('unpaid')->after('status');
            }

            if (! Schema::hasColumn('bookings', 'midtrans_order_id')) {
                $table->string('midtrans_order_id')->nullable()->after('payment_status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (Schema::hasColumn('bookings', 'payment_status')) {
                $table->dropColumn('payment_status');
            }
            if (Schema::hasColumn('bookings', 'midtrans_order_id')) {
                $table->dropColumn('midtrans_order_id');
            }
        });
    }
};