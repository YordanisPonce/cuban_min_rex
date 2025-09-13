<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Billing extends Model
{
    protected $fillable = [
        'phone',
        'address',
        'postal',
        'country'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
