<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;

class GuestUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'user@guest.com'],
            [
                'name' => 'Guest User',
                'password' => Hash::make('12345678'),
                'role' => 'user',
            ]
        );
    }
}
