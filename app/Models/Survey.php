<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Survey extends Model
{
    protected $fillable = [
        'user_id',
        'venue_id',
        'proposed_date',
        'confirmed_date',
        'proposed_time',
        'end_time',
        'confirmed_time',
        'notes',
        'admin_notes',
        'status',
    ];

    protected $casts = [
        'proposed_date'  => 'date',
        'confirmed_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }
}