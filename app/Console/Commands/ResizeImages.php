<?php

namespace App\Console\Commands;

use App\Models\Collection;
use App\Models\File;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Str;

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
            'packs' => Collection::all(),
        ];

        foreach ($items as $key => $value) {
            foreach ($value as $item) {
                $photo = null;

                switch ($key) {
                    case 'users':
                        $photo = $item->photo;
                        break;
                    
                    case 'files':
                        $photo = $item->poster;
                        break;
                    
                    default:
                        $photo = $item->image;
                        break;
                }

                if($photo){
                    $image = Image::read(Storage::disk('s3')->url($photo))->resize(300,200);

                    $ext = $photo->getClientOriginalExtension();
                    $newPhoto = 'images/'.Str::random() . '.' . $ext;

                    Storage::disk('s3')->put(
                        $newPhoto,
                        $image->encodeByExtension($ext, quality: 70)
                    );

                    Storage::disk('s3')->delete($photo);

                    switch ($key) {
                        case 'users':
                            $item->photo = $newPhoto;
                            break;
                        
                        case 'files':
                            $item->poster = $newPhoto;
                            break;
                        
                        default:
                            $item->image = $newPhoto;
                            break;
                    }
                    $item->save();
                }
            }
        }
    }
}
