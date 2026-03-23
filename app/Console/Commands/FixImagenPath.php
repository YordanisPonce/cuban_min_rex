<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class FixImagenPath extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fix-imagen-path';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        <?php

namespace App\Console\Commands;

use App\Models\Collection;
use App\Models\File;
use App\Models\Plan;
use App\Models\PlayList;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\ImageManager;

class FixImagenPath extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fix-images';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix all storaged images path.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $items = [
            'users' => User::all(),
            'files' => File::all(),
            'plans' => Plan::all(),
            'playlists' => PlayList::all(),
        ];

        $succefullyProcessed = 0;
        $errorProcessing = 0;

        $this->info('Start picture convertion to webp.');

        foreach ($items as $key => $value) {
            $this->info('Processing ' . $key . '...');
            foreach ($value as $item) {
                $photo = null;

                switch ($key) {
                    case 'users':
                        $photo = $item->photo;
                        break;
                    
                    case 'files':
                        $photo = $item->poster;
                        break;
                    
                    case 'playlists':
                        $photo = $item->cover;
                        break;
                    
                    default:
                        $photo = $item->image;
                        break;
                }
                
                if($photo){
                    try {
                        switch ($key) {
                            case 'users':
                                $item->photo = 'images/'.$photo;
                                break;
                            
                            case 'files':
                                $item->poster = 'images/'.$photo;
                                break;
                        
                            case 'playlists':
                                $item->cover = 'images/'.$photo;
                                break;
                            
                            default:
                                $item->image = 'images/'.$photo;
                                break;
                        }
                        $item->save();
                        $succefullyProcessed++;
                    } catch (\Throwable $th) {
                        $errorProcessing++;
                        $this->error('Error processing ' . $photo . ': ' . $th->getMessage());
                    }
                    
                }
            }
        }

        $this->info('Processing completed. Successfully processed: ' . $succefullyProcessed . ', Errors: ' . $errorProcessing);
    }
}
