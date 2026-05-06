<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name'     => 'Fadhi Ramadhan',
                'email'    => 'fadhi@gmail.com',
                'phone'    => '081234567891',
                'password' => Hash::make('password'),
                'role'     => 'user',
            ],
            [
                'name'     => 'Davino Pratama Arhan',
                'email'    => 'davino@gmail.com',
                'phone'    => '081234567892',
                'password' => Hash::make('password'),
                'role'     => 'user',
            ],
            [
                'name'     => 'Nayubi A.R',
                'email'    => 'nayubi@gmail.com',
                'phone'    => '081234567893',
                'password' => Hash::make('nayubi'),
                'role'     => 'user',
            ],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                ['email' => $user['email']],
                $user
            );
        }
    }
}