<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Enums\UserRole;
use App\Enums\AdminUser;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => AdminUser::EMAIL->value],
            [
                'name' => AdminUser::NAME->value,
                'password' => Hash::make(AdminUser::PASSWORD->value),
                'role' => UserRole::ADMIN->value,
            ]
        );
    }
}
