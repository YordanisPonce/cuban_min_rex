<?php

namespace App\Console\Commands;

use App\Models\File;
use Illuminate\Console\Command;

class SetAllFileCategoryIdToNull extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'file:set-category-id-to-null';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set Category ID null of all files';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Opteniendo todos los Remxises....');
        $files = File::all();
        $this->info(count($files) . " Remixes encontrados.");
        $this->info(count($files->whereNotNull('category_id')) . " Remixes con fuerte relación con categoría.");
        $this->info("Eliminado las relaciones directas con categoría ....");
        $s = 0;
        $c = 0;
        foreach ($files as $f) {
            try {
                $f->update(['category_id' => null, 'collection_id' => null]);
                $s++;
            } catch (\Throwable $th) {
                $c++;
            }
        }
        $this->info("Proceso terminado. $s completado, $c fallido.");
        $letFiles = File::all();
        $this->info(count($letFiles) . " Remixes resultantes.");
        $this->info(count($letFiles->whereNotNull('category_id')) . " Remixes con fuerte relación con categoría.");
    }
}
