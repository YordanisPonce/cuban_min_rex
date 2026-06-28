<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlaylistZipRequest extends Model
{
    protected $fillable = [
        'uuid',
        'user_id',
        'play_list_id',
        'status',
        'zip_file_name',
        's3_path',
        'error_message',
        'tracks_added',
        'tracks_total',
        'download_registered',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'download_registered' => 'boolean',
            'expires_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function playList(): BelongsTo
    {
        return $this->belongsTo(PlayList::class);
    }

    public function isReady(): bool
    {
        return $this->status === 'ready'
            && $this->s3_path
            && ($this->expires_at === null || $this->expires_at->isFuture());
    }

    public function isInProgress(): bool
    {
        return in_array($this->status, ['pending', 'processing'], true);
    }
}
