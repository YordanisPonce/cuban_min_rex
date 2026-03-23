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

class ResizeImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:resize-images';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Resize all storaged images to optimize.';

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
                    $imagePath = Storage::disk('s3')->get($photo);

                    $this->info('Processing ' . $photo . '...');
                    
                    try {
                        $manager = new ImageManager(Driver::class);
                        $image = $manager->read($imagePath)->scaleDown(width: 200, height: 200);
                        $encoded = $image->encode(new WebpEncoder(quality: 50));
                        $webpPath = 'images/'.Str::random().'.webp';
                        $encoded->save(Storage::disk('public')->path($webpPath));

                        $stream = fopen(Storage::disk('public')->path($webpPath), 'r');
                        Storage::disk('s3')->writeStream($webpPath, $stream);
                            if (is_resource($stream))
                                fclose($stream);
                        
                        Storage::disk('public')->delete($webpPath);
                        Storage::disk('s3')->delete($photo);

                        switch ($key) {
                            case 'users':
                                $item->photo = $webpPath;
                                break;
                            
                            case 'files':
                                $item->poster = $webpPath;
                                break;
                        
                            case 'playlists':
                                $item->cover = $webpPath;
                                break;
                            
                            default:
                                $item->image = $webpPath;
                                break;
                        }

                        $item->save();
                        
                        $succefullyProcessed++;
                        $this->info('Finished ' . $photo . ' processing. Successfully converted to webp and updated the record.');
                    } catch (\Throwable $th) {
                        $errorProcessing++;
                        $this->warn('Error processing ' . $photo . '. Skipping. Error: ' . $th->getMessage());
                    }
                    
                }
            }
        }

        $this->info('Processing completed. Successfully processed: ' . $succefullyProcessed . ', Errors: ' . $errorProcessing);
    }
}
