<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LegalText extends Model
{
    protected $fillable = [
        'legal',
        'cookies',
        'privacy',
        'terms',
    ];
}
