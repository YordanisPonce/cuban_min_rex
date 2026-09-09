<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;

class WorkerUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'chinadj@cubanpool.com'],
            [
                'name' => 'China DJ',
                'password' => Hash::make('12345678'),
                'email_verified_at' => Carbon::now(),
                'role' => 'worker',
            ]
        );
        User::firstOrCreate(
            ['email' => 'yankidj@cubanpool.com'],
            [
                'name' => 'Yanki DJ',
                'password' => Hash::make('12345678'),
                'email_verified_at' => Carbon::now(),
                'role' => 'worker',
            ]
        );
        User::firstOrCreate(
            ['email' => 'aitanadj@cubanpool.com'],
            [
                'name' => 'Aitana DJ',
                'password' => Hash::make('12345678'),
                'email_verified_at' => Carbon::now(),
                'role' => 'worker',
            ]
        );
    }
}
