<?php

namespace Database\Seeders;

use App\Models\Package;
use Illuminate\Database\Seeder;

class PackageSeeder extends Seeder
{
    public function run(): void
    {
        $packages = [
            [
                'name'        => 'Basic Package',
                'slug'        => 'basic-package',
                'price'       => 25000000,
                'price_label' => 'Rp 25jt',
                'tagline'     => 'Untuk acara sederhana dengan kebutuhan dasar',
                'features'    => ['Dekorasi dasar', 'Catering 100 pax', 'Dokumentasi foto'],
                'is_popular'  => false,
                'is_active'   => true,
                'color'       => '#c9a861',
                'sort_order'  => 1,
            ],
            [
                'name'        => 'Premium Package',
                'slug'        => 'premium-package',
                'price'       => 40000000,
                'price_label' => 'Rp 40jt',
                'tagline'     => 'Pilihan favorit dengan dekorasi & MC profesional',
                'features'    => ['Dekorasi full tematik', 'Catering 300 pax', 'Foto & video liputan'],
                'is_popular'  => true,
                'is_active'   => true,
                'color'       => '#c9a861',
                'sort_order'  => 2,
            ],
            [
                'name'        => 'Luxury Package',
                'slug'        => 'luxury-package',
                'price'       => 60000000,
                'price_label' => 'Rp 60jt',
                'tagline'     => 'Paket lengkap untuk acara mewah & berkesan',
                'features'    => ['Dekorasi eksklusif', 'Catering 500 pax', 'Panggung & hiburan'],
                'is_popular'  => false,
                'is_active'   => true,
                'color'       => '#c9a861',
                'sort_order'  => 3,
            ],
            // Contoh paket inactive (untuk demo "list panjang")
            [
                'name'        => 'Diamond Package',
                'slug'        => 'diamond-package',
                'price'       => 90000000,
                'price_label' => 'Rp 90jt',
                'tagline'     => 'Paket eksklusif untuk acara skala besar dengan VIP treatment',
                'features'    => ['Wedding Organizer full team', 'Catering 800 pax', 'Live band internasional', 'Drone documentation'],
                'is_popular'  => false,
                'is_active'   => false,
                'color'       => '#c9a861',
                'sort_order'  => 4,
            ],
            [
                'name'        => 'Bronze Package',
                'slug'        => 'bronze-package',
                'price'       => 15000000,
                'price_label' => 'Rp 15jt',
                'tagline'     => 'Paket hemat untuk acara intimate keluarga',
                'features'    => ['Dekorasi simple', 'Catering 50 pax', 'Foto session'],
                'is_popular'  => false,
                'is_active'   => false,
                'color'       => '#c9a861',
                'sort_order'  => 5,
            ],
        ];

        foreach ($packages as $pkg) {
            Package::updateOrCreate(['slug' => $pkg['slug']], $pkg);
        }
    }
}