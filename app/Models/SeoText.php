<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class SeoText extends Model
{
    protected $fillable = [
        'app_name',
        'app_description',
        'app_logo',
        'contat_email',
        'contact_phone',
        'contact_instagram',
        'contact_youtube',
        'contact_facebook',
    ];

    public function logoUrl()
    {
        return $this->app_logo ? Storage::disk('s3')->url($this->app_logo) : null;
    }
}
