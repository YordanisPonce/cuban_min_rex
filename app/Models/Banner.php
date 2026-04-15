<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Banner extends Model
{
    protected $fillable = [
        'path',
        'active'
    ];
    
    public function image(): string
    {
        return Storage::disk('s3')->url($this->path);
    }
}
