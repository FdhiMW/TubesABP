<?php

namespace App\Constants;

class BookingPackage
{
    public const BASIC   = 'basic';
    public const PREMIUM = 'premium';
    public const LUXURY  = 'luxury';

    /**
     * Daftar lengkap semua paket.
     */
    public static function all(): array
    {
        return [
            self::BASIC => [
                'key'         => self::BASIC,
                'name'        => 'Basic Package',
                'price'       => 25000000,
                'price_label' => 'Rp 25jt',
                'tagline'     => 'Untuk acara sederhana dengan kebutuhan dasar',
                'is_popular'  => false,
                'features'    => [
                    'Dekorasi dasar',
                    'Catering 100 pax',
                    'Dokumentasi foto',
                ],
                'color' => '#c9a861',
            ],
            self::PREMIUM => [
                'key'         => self::PREMIUM,
                'name'        => 'Premium Package',
                'price'       => 40000000,
                'price_label' => 'Rp 40jt',
                'tagline'     => 'Pilihan favorit dengan dekorasi & MC profesional',
                'is_popular'  => true,
                'features'    => [
                    'Dekorasi full tematik',
                    'Catering 300 pax',
                    'Foto & video liputan',
                ],
                'color' => '#c9a861',
            ],
            self::LUXURY => [
                'key'         => self::LUXURY,
                'name'        => 'Luxury Package',
                'price'       => 60000000,
                'price_label' => 'Rp 60jt',
                'tagline'     => 'Paket lengkap untuk acara mewah & berkesan',
                'is_popular'  => false,
                'features'    => [
                    'Dekorasi eksklusif',
                    'Catering 500 pax',
                    'Panggung & hiburan',
                ],
                'color' => '#c9a861',
            ],
        ];
    }

    /** Ambil 1 paket */
    public static function get(string $key): ?array
    {
        return self::all()[$key] ?? null;
    }

    /** Cek validitas key paket */
    public static function exists(string $key): bool
    {
        return array_key_exists($key, self::all());
    }

    /** Nama paket untuk display */
    public static function name(string $key): string
    {
        return self::get($key)['name'] ?? ucfirst($key);
    }

    /** Harga paket */
    public static function price(string $key): int
    {
        return self::get($key)['price'] ?? 0;
    }

    /** List key untuk validasi 'in:basic,premium,luxury' */
    public static function keys(): array
    {
        return array_keys(self::all());
    }
}