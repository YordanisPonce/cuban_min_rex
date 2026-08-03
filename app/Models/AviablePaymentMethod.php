<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AviablePaymentMethod extends Model
{
    protected $fillable = [
        'stripe',
        'paypal',
    ];
}
