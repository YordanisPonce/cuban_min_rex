<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'credit_card_info',
        'confirmation_phone',
        'confirmation_email',
        'currency_convertion_rate',
        'eltoque_api_token',
    ];

    protected $casts = [
        'currency_convertion_rate' => 'decimal:2',
        'eltoque_api_token' => 'encrypted',
    ];
}
