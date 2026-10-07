<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DownloadPack extends Model
{
    protected $fillable = [
        'extra_downloads',
        'price',
        'active',
    ];
}
