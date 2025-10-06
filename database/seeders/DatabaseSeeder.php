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
        User::factory()->create([
            'name' => 'User A',
            'email' => 'user_a@miniwallet.com',
            'password' => bcrypt('123123123'),
            'balance' => 1000
        ]);

        User::factory()->create([
            'name' => 'User B',
            'email' => 'user_b@miniwallet.com',
            'password' => bcrypt('123123123'),
            'balance' => 1000
        ]);
    }
}
