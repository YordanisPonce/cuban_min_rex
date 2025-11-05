<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\File;
use App\Models\Collection;
use App\Models\Category;

class FilesSeeder extends Seeder
{
    public function run(): void
    {
        $pop   = Category::where('name', 'Pop')->first();
        $rock  = Category::where('name', 'Rock')->first();
        $lat   = Category::where('name', 'Latino')->first();

        $hitsPop     = Collection::where('name', 'Hits Pop 2020s')->first();
        $rockClasico = Collection::where('name', 'Rock Clásico')->first();
        $latinoVibra = Collection::where('name', 'Latino Vibra')->first();
        $focusChill  = Collection::where('name', 'Focus & Chill')->first();
        $indieDisc   = Collection::where('name', 'Indie Descubrimientos')->first();

        $rows = [
            // En colecciones CON categoría (la canción heredará conceptualmente esa categoría en tu lógica de negocio)
            ['name' => 'Shimmer Nights',   'collection_id' => optional($hitsPop)->id, 'category_id' => optional($pop)->id,  'user_id' => 1, 'price' => 0,    'file' => 'audio/shimmer-nights.mp3', 'original_file' => 'audio/shimmer-nights.mp3'],
            ['name' => 'Neon Waves',       'collection_id' => optional($hitsPop)->id, 'category_id' => optional($pop)->id,  'user_id' => 1, 'price' => 0,    'file' => 'audio/neon-waves.mp3', 'original_file' => 'audio/neon-waves.mp3'],

            ['name' => 'Thunder Road',     'collection_id' => optional($rockClasico)->id, 'category_id' => optional($rock)->id,'user_id' => 1, 'price' => 0, 'file' => 'audio/thunder-road.mp3', 'original_file' => 'audio/thunder-road.mp3'],
            ['name' => 'Echoes',           'collection_id' => optional($rockClasico)->id, 'category_id' => optional($rock)->id,'user_id' => 1, 'price' => 0, 'file' => 'audio/echoes.mp3', 'original_file' => 'audio/echoes.mp3'],

            ['name' => 'Baila Conmigo',    'collection_id' => optional($latinoVibra)->id, 'category_id' => optional($lat)->id,'user_id' => 1, 'price' => 1.99, 'file' => 'audio/baila-conmigo.mp3', 'original_file' => 'audio/baila-conmigo.mp3'],
            ['name' => 'Ritmo del Sol',    'collection_id' => optional($latinoVibra)->id, 'category_id' => optional($lat)->id,'user_id' => 1, 'price' => 1.49, 'file' => 'audio/ritmo-del-sol.mp3', 'original_file' => 'audio/ritmo-del-sol.mp3'],

            // En colecciones SIN categoría (category_id null permitido)
            ['name' => 'Lo-Fi Stream',     'collection_id' => optional($focusChill)->id, 'category_id' => null, 'user_id' => 1, 'price' => 0,    'file' => 'audio/lofi-stream.mp3', 'original_file' => 'audio/lofi-stream.mp3'],
            ['name' => 'Deep Focus',       'collection_id' => optional($focusChill)->id, 'category_id' => null, 'user_id' => 1, 'price' => 0,    'file' => 'audio/deep-focus.mp3', 'original_file' => 'audio/deep-focus.mp3'],

            // Canciones sueltas SIN colección (permitido)
            ['name' => 'Stand Alone Pop',  'collection_id' => null, 'category_id' => optional($pop)->id,  'user_id' => 1, 'price' => 0, 'file' => 'audio/standalone-pop.mp3', 'original_file' => 'audio/standalone-pop.mp3'],
            ['name' => 'Free Rock',        'collection_id' => null, 'category_id' => optional($rock)->id, 'user_id' => 1, 'price' => 0, 'file' => 'audio/free-rock.mp3', 'original_file' => 'audio/free-rock.mp3'],
        ];

        foreach ($rows as $row) {
            File::firstOrCreate(
                ['name' => $row['name'], 'user_id' => $row['user_id']],
                $row
            );
        }
    }
}
