<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Venue extends Model
{
    protected $table = 'venues';

    protected $fillable = [
        'name',
        'description',
        'location',
        'capacity',
        'price_per_day',
        'facilities',
        'photos',
        'status',
    ];

    protected $casts = [
        'facilities'    => 'array',
        'photos'        => 'array',
        'price_per_day' => 'decimal:2',
    ];

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function surveys(): HasMany
    {
        return $this->hasMany(Survey::class);
    }
}