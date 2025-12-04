<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        try {
            User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => Hash::make('12345678'),
            ]);
        } catch (\Throwable $th) {
            //throw $th;
        }

        $this->call([
            AdminUserSeeder::class,
            GuestUserSeeder::class,
            CategoriesSeeder::class,
            CollectionSeeder::class,
            FilesSeeder::class,
        ]);
    }
}
