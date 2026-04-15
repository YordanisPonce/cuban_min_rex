<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'title',
        'mesage',
        'was_readed'
    ];

    public function markAsRead(){
        $this->update([
            'was_readed' => true
        ]);
    }
}
