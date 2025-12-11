<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            'name' => 'TestUser1',
            'email' => 'testuser1@example.com',
            'password' => '$2y$12$x6rtAKZw4lt9Tis4u8qrJe55GUVTq6TS25s2SdeRlgUOzBOoNg1MC',
            'role' => 'admin',
        ]);

        DB::table('users')->insert([
            'name' => 'TestUser2',
            'email' => 'testuser2@example.com',
            'password' => '$2y$12$x6rtAKZw4lt9Tis4u8qrJe55GUVTq6TS25s2SdeRlgUOzBOoNg1MC',
        ]);

        // 149 nepgebruikers
        User::factory()->count(150)->create();
    }
}
