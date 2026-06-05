<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Package extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'price',
        'price_label',
        'tagline',
        'features',
        'is_popular',
        'is_active',
        'color',
        'sort_order',
    ];

    protected $casts = [
        'features'    => 'array',
        'is_popular'  => 'boolean',
        'is_active'   => 'boolean',
        'price'       => 'decimal:2',
    ];

    /**
     * Auto-generate slug saat create.
     */
    protected static function booted(): void
    {
        static::saving(function (Package $package) {
            if (empty($package->slug)) {
                $package->slug = Str::slug($package->name);
            }
        });
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Scope: hanya paket yang sedang aktif.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }

    /**
     * Hitung jumlah paket yang sedang aktif.
     */
    public static function activeCount(): int
    {
        return self::where('is_active', true)->count();
    }

    public const MAX_ACTIVE = 3;
}