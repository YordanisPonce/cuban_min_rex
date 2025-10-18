<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Collection extends Model
{
    protected $fillable = [
        'name',
        'user_id',
        'category_id',
        'image'
    ];

    public function files(): HasMany
    {
        return $this->hasMany(File::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    protected function image(): Attribute
    {

        return Attribute::make(
            get: fn($item) => $item ? Storage::disk('public')->url($item) : $item
        );
    }
}
