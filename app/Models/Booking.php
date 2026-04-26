<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
        'total_price',
        'status'
    ];
}
