<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Muhammad Arslan',
        //     'email' => 'superadmin@example.com',
        //     'email_verified_at' => now(),
        //     'password' => Hash::make('admin@1234'),
        //     'role' => 1,
        //     'remember_token' => Str::random(10),
        //     'created_at' => now(),
        //     'updated_at' => now(),
        // ]);
    }
}
