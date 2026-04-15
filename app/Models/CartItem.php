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

    public function cover(): string
    {
        if($this->file) return $this->file->poster ?? $this->file->user->photo ?? config('app.logo');

        if($this->playlist()) return $this->playlist->cover ?? $this->playlist->user->photo ?? config('app.logo');

        return $this->playlistItem->playlist->cover ?? $this->playlistItem->playlist->user->photo ?? config('app.logo');
    }

    public function name(): string
    {
        if($this->file) return 'REMIX: '.$this->file->name;

        if($this->playlist()) return 'PLAYLIST: '.$this->playlist->name;

        return 'PLAYLIST SOUND: '.$this->playlistItem->title;
    }

    public function price(): float
    {
        if($this->file) return $this->file->price;

        if($this->playlist()) return $this->playlist->price;

        return $this->playlistItem->price;
    }

    public function dj(): string
    {
        if($this->file) return $this->file->user->name;

        if($this->playlist()) return $this->playlist->user->name;

        return $this->playlistItem->playlist->user->name;
    }

    public function removeRoute(): string {
        if($this->file) return route('file.remove.cart', $this->file->id);

        if($this->playlist()) return route('playlist.add.cart', str_replace(' ', '_', $this->playlist->name));

        return route('playlist.add.item.cart', [str_replace(' ', '_', $this->playlist->name), $this->playlistItem->id]);
    }
}
