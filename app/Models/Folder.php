<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

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

    protected function cover_image(): Attribute
    {

        $isFrontend = request()->input('is_frontend');

        return Attribute::make(
            get: fn($item) => $item && $isFrontend ? Storage::disk('s3')->url($item) : $item
        );
    }
}
