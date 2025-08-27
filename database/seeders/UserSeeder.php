<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'dwiandika@gmail.com'], 
            [
                'name'  => 'Dwi Andika',
                'password' => Hash::make('dwiandika123'),
                'role'   => "admin",
            ]
        );

        User::firstOrCreate(
            ['email' => 'anggapradita@gmail.com'],
            [
                'name'  => 'Angga Pradita',
                'password' => Hash::make('angga123'),
                'role'   => "tenants",
            ]
        );
    }
}
