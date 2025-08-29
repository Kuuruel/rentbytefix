<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::create([
            'name' => 'rentbyte',
            'email' => 'rentbyte@gmail.com',
            'password' => bcrypt('rentbyte123'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'kuuruel',
            'email' => 'kuuruel@gmail.com',
            'password' => bcrypt('kuuruel'),
            'role' => 'admin',
        ]);
    }
}
