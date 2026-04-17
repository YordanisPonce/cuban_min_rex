<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        'user_id',
        'dj_id',
        'rating',
        'comment',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function dj()
    {
        return $this->belongsTo(User::class, 'dj_id');
    }
}
