<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationSettings extends Model
{
    protected $fillable = [
        'user_id',
        'new_remixes',
        'new_playlists',
        'new_followers',
        'promos',
    ];
}
