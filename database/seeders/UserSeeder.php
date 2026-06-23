<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin account
        User::firstOrCreate(
            ['email' => 'admin@itshop.com'],
            [
                'name'     => 'Admin ITShop',
                'password' => Hash::make('password'),
                'role'     => 'admin',
            ]
        );

        // Cashier / petugas account
        User::firstOrCreate(
            ['email' => 'petugas@itshop.com'],
            [
                'name'     => 'Petugas Kasir',
                'password' => Hash::make('password'),
                'role'     => 'petugas',
            ]
        );
    }
}
