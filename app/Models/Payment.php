<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'user_id','paypal_response','item_id','sender_batch_id','amount','currency','email','note'
    ];
}
