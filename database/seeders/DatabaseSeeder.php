<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;



class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'rentbyte',
            'email' => 'rentbyte@gmail.com',
            'password' => bcrypt('rentbyte123'),
            'role' => 'admin',
        ]);

    }
}
