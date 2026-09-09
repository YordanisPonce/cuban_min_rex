<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Background extends Model
{
    protected $fillable = [
        'path',
        'active'
    ];
    
    public function image(): string
    {
        return $this->path ? Storage::disk('s3')->url($this->path): null;
    }
}
