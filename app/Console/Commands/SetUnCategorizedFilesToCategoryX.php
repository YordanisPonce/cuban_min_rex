<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\File;
use Illuminate\Console\Command;

class SetUnCategorizedFilesToCategoryX extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'files:set-category {--category=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set all Uncategorized Files to the Category selected.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $category_name = $this->option('category') ?? null;
            if (!$category_name) {
                $this->error('Categoría requerida');
            }
            $this->info('Buscando la categoría indicada...');
            $category = Category::where('name', $category_name)->first();
            if (!$category) {
                $this->error('Categoría no encontrada');
                return;
            }
            $this->info("Categoría encontrada: $category->name, con id: $category->id");
            $this->info('Buscando Archivos sin Categorías ...');
            $uncategorized = File::doesntHave('categories')->get();
            if (count($uncategorized) === 0) {
                $this->warn('No se encontraron archivos sin categorías.');
                return;
            }
            $this->info(count($uncategorized) . ' archivos sin categoría encontrados.');
            $this->info("Asignando $category_name a los archivos encontrados");
            $success = 0;
            $fail = 0;
            foreach ($uncategorized as $file) {
                try {
                    $file->categories()->attach($category->id);
                    $success++;
                } catch (\Throwable $th) {
                    $fail++;
                }
            }
            $this->info("Categoría $category_name asignada a $success archivos. $fail errores.");
        } catch (\Throwable $th) {
            $this->error('Error al ejecutar comando: ' . $th->getMessage());
        }
    }
}
