<?php

namespace App\Console\Commands;

use App\Models\File;
use App\Models\PlayListItem;
use Illuminate\Console\Command;

class ChangeAllFilesPrice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:change-prices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Change all items prices to fixed $4.99';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Get all Remixes(Mix and Exclusives), Videos and Playlist items...');
        $remixes = File::audios()->get();
        $videos = File::videos()->get();
        $items = PlayListItem::all();
        $this->info("Finded ".count($remixes)." remixes, ".count($videos)." videos and ".count($items)." playlist items.");
        $this->info('Change Remixes(Mix and Exclusives) prices...');
        $success = 0;
        $fail = 0;
        foreach ($remixes as $r) {
            try {
                $r->update(['price' => 4.99]);
                $success++;
            } catch (\Throwable $th) {
                $fail++;
            }
        }
        $this->info("$success Remixes Prices Changed. $fail changes failed.");
        $this->info('Change Videos prices...');
        $success = 0;
        $fail = 0;
        foreach ($videos as $r) {
            try {
                $r->update(['price' => 4.99]);
                $success++;
            } catch (\Throwable $th) {
                $fail++;
            }
        }
        $this->info("$success Videos Prices Changed. $fail changes failed.");
        $this->info('Change Playlist items prices...');
        $success = 0;
        $fail = 0;
        foreach ($items as $r) {
            try {
                $r->update(['price' => 4.99]);
                $success++;
            } catch (\Throwable $th) {
                $fail++;
            }
        }
        $this->info("$success Videos Prices Changed. $fail changes failed.");
    }
}
