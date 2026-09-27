<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Category extends Model
{
    protected $fillable = [
        'name',
        'user_id',
        'is_general',
        'show_in_landing',
        'cover',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function collections(): HasMany{
        return $this->hasMany(Collection::class);
    }

    public function files(): BelongsToMany{
        return $this->belongsToMany(File::class, 'category_files', 'category_id', 'file_id');
    }

    public function getCoverUrl() : String {
        return $this->cover ? Storage::disk('s3')->url($this->cover) : $this->user?->getPhotoUrl() ?? asset('img/logo_alter.png');
    }
}
