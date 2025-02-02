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
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'role' => 'Administrateur',
            'password' => bcrypt('password'),
        ]);

        User::factory()->create([
            'name' => 'IFA',
            'email' => 'ifa@ifa.com',
            'role' => 'Boutique',
            'password' => bcrypt('password'),
        ]);

        User::factory()->create([
            'name' => 'Aymard Mboula',
            'email' => 'client@client.com',
            'role' => 'Client',
            'password' => bcrypt('password'),
        ]);
    }
}
