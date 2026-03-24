<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Download extends Model
{
    protected $fillable = [
        'user_id',
        'file_id',
        'liquidated',
        'play_list_id',
        'play_list_item_id',
    ];

    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    public function file(): BelongsTo
    {
        return $this->belongsTo(File::class);
    }

    public function playlist(): BelongsTO
    {
        return $this->belongsTo(PlayList::class, 'play_list_id');
    }

    public function playlistItem(): BelongsTO
    {
        return $this->belongsTo(PlayListItem::class, 'play_list_item_id');
    }
}
