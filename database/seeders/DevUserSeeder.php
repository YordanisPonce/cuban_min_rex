<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Enums\UserRole;
use App\Enums\AdminUser;

class DevUserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'developer@cubanpool.com'],
            [
                'name' => 'Developer',
                'password' => Hash::make('dev1234*'),
                'role' => 'developer',
            ]
        );
    }
}
