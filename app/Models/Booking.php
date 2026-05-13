<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    protected $table = 'bookings';

    protected $fillable = [
        'booking_code',
        'user_id',
        'venue_id',
        'event_date',
        'end_date',
        'event_time',
        'end_time',
        'guest_count',
        'package_id',
        'total_price',
        'status',
        'payment_status',
        'midtrans_order_id',
        'payment_reference',
        'cancellation_reason',
        'cancelled_at',
    ];

    protected $casts = [
        'event_date'   => 'date',
        'end_date'     => 'date',
        'cancelled_at' => 'datetime',
        'total_price'  => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    /**
     * Helper: nama paket untuk display (handle null).
     */
    public function getPackageNameAttribute(): string
    {
        return $this->package?->name ?? '-';
    }
}