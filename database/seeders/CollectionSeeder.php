<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Collection;
use App\Models\Category;

class CollectionSeeder extends Seeder
{
    public function run(): void
    {
        $pop = Category::where('name', 'Pop')->first();
        $rock = Category::where('name', 'Rock')->first();
        $lat = Category::where('name', 'Latino')->first();

        $data = [
            // Con categoría
            ['name' => 'Hits Pop 2020s', 'user_id' => 1, 'category_id' => optional($pop)->id],
            ['name' => 'Rock Clásico', 'user_id' => 1, 'category_id' => optional($rock)->id],
            ['name' => 'Latino Vibra', 'user_id' => 1, 'category_id' => optional($lat)->id],
        ];

        foreach ($data as $row) {
            Collection::firstOrCreate(
                ['name' => $row['name'], 'user_id' => $row['user_id']],
                $row
            );
        }
    }
}
