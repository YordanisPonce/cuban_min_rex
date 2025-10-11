<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sale extends Model
{
    protected $fillable = [
        'user_id',
        'file_id',
        'amount',
        'user_amount',
        'admin_amount',
        'status'
    ];

    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    public function file(): BelongsTo
    {
        return $this->belongsTo(File::class);
    }
}
