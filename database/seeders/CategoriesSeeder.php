<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategoriesSeeder extends Seeder
{
    public function run(): void
    {
        // Evita duplicados simples por nombre
        $data = [
            ['name' => 'Pop', 'user_id' => 1, 'is_general' => true, 'show_in_landing' => true],
            ['name' => 'Rock', 'user_id' => 1, 'is_general' => true, 'show_in_landing' => true],
            ['name' => 'Electrónica', 'user_id' => 1, 'is_general' => true, 'show_in_landing' => true],
            ['name' => 'Latino', 'user_id' => 1, 'is_general' => true, 'show_in_landing' => true],
            ['name' => 'Indie', 'user_id' => 1, 'is_general' => true, 'show_in_landing' => false],
        ];

        foreach ($data as $row) {
            Category::firstOrCreate(
                ['name' => $row['name']],
                $row
            );
        }
    }
}
