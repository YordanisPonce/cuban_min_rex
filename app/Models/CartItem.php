<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $fillable = [
        'cart_id',
        'file_id',
        'play_list_id',
        'play_list_item_id',
        'amount',
    ];

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function file()
    {
        return $this->belongsTo(File::class);
    }

    public function playlist()
    {
        return $this->belongsTo(PlayList::class, 'play_list_id');
    }

    public function playlistItem()
    {
        return $this->belongsTo(PlayListItem::class, 'play_list_item_id');
    }
}
