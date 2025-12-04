<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'plan_id',
        'file_id',
        'stripe_session_id',
        'stripe_payment_intent',
        'amount',
        'status',
        'paid_at',
        'expires_at'
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }
    public function file(): BelongsTo
    {
        return $this->belongsTo(File::class);
    }

    public function order_items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
