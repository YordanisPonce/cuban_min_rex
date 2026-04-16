<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Folder extends Model
{
    protected $fillable = [
        'name',
        'description',
        'cover_image',
        'type',
    ];

    public function files()
    {
        return $this->hasMany(File::class, 'folder_id');
    }

    public function playlists()
    {
        return $this->hasMany(Playlist::class, 'folder_id');
    }

    protected function getCoverUrl()
    {

        return $this->cover_image ? Storage::disk('s3')->url($this->cover_image) : null;
    }
}
