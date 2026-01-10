<?php

namespace App\Console\Commands;

use App\Models\File;
use Illuminate\Console\Command;

class RefactorFilesCategoriesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:refactor-files-categories-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Attach Files category_id to categories';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $files = File::all();

        foreach ($files as $file) {
            if ($file->category && !$file->categories->find($file->category->id)) {
                $file->categories()->attach($file->category->id);
            }
        }
    }
}
