<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class File extends Model
{
    protected $fillable = [
        'name',
        'collection_id',
        'category_id',
        'user_id',
        'file',
    ];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function collection(): BelongsTo { return $this->belongsTo(Collection::class); }
    public function category(): BelongsTo { return $this->belongsTo(Category::class); }
}
