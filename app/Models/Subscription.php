<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'stripe_id',
        'stripe_status',
        'stripe_price',
        'quantity',
        'trial_ends_at',
        'max_downloads',
        'extra_downloads',
        'consume_extra_downloads',
        'plan_name',
        'plan_price',
        'ends_at',
        'candeled_at'
    ];
}
