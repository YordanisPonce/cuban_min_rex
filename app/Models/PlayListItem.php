<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class PlayListItem extends Model
{
    protected $table = 'play_list_items';

    protected $fillable = [
        'cover',
        'file_path',
        'title',
        'price',
        'play_list_id'
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [                    
            'price' => 'decimal:2',
        ];
    }

    public function playList(): BelongsTo
    {
        return $this->belongsTo(PlayList::class);
    }

    public function downloads(): HasMany
    {
        return $this->hasMany(Download::class);
    }

    public function getFileUrl()
    {
        return Storage::disk('s3')->url($this->file_path);
    }
    
    public function getCoverUrl()
    {
        return Storage::disk('s3')->url($this->cover);
    }

    /**
     * Verify if the playlist item has in current user cart
     * 
     * @return bool
     */
    public function isInCart(): bool
    {
        $cart = Cart::get_current_cart();
        $cartItem = $cart->cart_items()->where('play_list_item_id', $this->id)->first();
        
        return $cartItem !== null;
    
    } 
}
