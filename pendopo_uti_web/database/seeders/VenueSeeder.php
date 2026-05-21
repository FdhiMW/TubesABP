<?php

namespace Database\Seeders;

use App\Models\Venue;
use Illuminate\Database\Seeder;

class VenueSeeder extends Seeder
{
    public function run(): void
    {
        Venue::updateOrCreate(
            ['name' => 'Pendopo Utama UTI'],
            [
                'description'   => 'Gedung serbaguna eksklusif dengan arsitektur klasik dan sentuhan modern. '
                                 . 'Cocok untuk pernikahan, resepsi, gathering perusahaan, dan acara formal lainnya. '
                                 . 'Dilengkapi dengan AC, sound system premium, dan dekorasi mewah.',
                'location'      => 'Jl. Raya Pendopo No. 1, Bandung, Jawa Barat',
                'capacity'      => 500,
                'price_per_day' => 25000000,
                'facilities'    => [
                    'AC Sentral',
                    'Sound System Premium',
                    'Panggung Utama',
                    'Lighting Profesional',
                    'Parkir 200 mobil',
                    'Ruang Rias Pengantin',
                    'Ruang VIP',
                    'WiFi Gratis',
                    'Genset Cadangan',
                    'Toilet Premium',
                ],
                'photos'        => [
                    'venues/pendopo-1.jpg',
                    'venues/pendopo-2.jpg',
                    'venues/pendopo-3.jpg',
                ],
                'status'        => 'active',
            ]
        );
    }
}