<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name'     => 'Admin Pendopo Uti',
                'phone'    => '081234567890',
                'password' => Hash::make('admin'),
                'role'     => 'admin',
            ]
        );
    }
}