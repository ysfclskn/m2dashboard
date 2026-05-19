<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $demo = User::firstOrCreate(
            ['email' => 'demo@example.com'],
            ['name' => 'Guild Master', 'password' => Hash::make('password')]
        );

        User::firstOrCreate(
            ['email' => 'alice@example.com'],
            ['name' => 'Alice Archer', 'password' => Hash::make('password')]
        );

        User::firstOrCreate(
            ['email' => 'bob@example.com'],
            ['name' => 'Bob Builder', 'password' => Hash::make('password')]
        );

        User::firstOrCreate(
            ['email' => 'carol@example.com'],
            ['name' => 'Carol Caster', 'password' => Hash::make('password')]
        );
    }
}
