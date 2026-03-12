<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class Cart extends Model
{
    protected $fillable = [
        'user_id',
        'uuid',
        'items'
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'items' => 'array',
        ];
    }

    public function cart_items()
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Get the total amount in the cart.
     */
    public function get_cart_count() {
        $count = 0;
        foreach($this->cart_items as $item) {
            if($item->file) {
                $count += $item->file->price;
            } else if($item->playlist) {
                $count += $item->playlist->price;
            } else if($item->playlistItem) {
                $count += $item->playlistItem->price;
            }
        }
        return $count;
    }

    static function get_current_cart(): Cart|null {
        $user = Auth::user() ?? null;
        $cart = null;
        if($user){
            $cart = $user->cart;
            if(!$cart) {
                $cart = new Cart();
                $cart->user_id = $user->id;
                $cart->save();
            }
        } else {
            $unique_id = session()->get('unique_id');
            if ($unique_id) {
                $cart = Cart::where('uuid', $unique_id)->first();
            } else {
                $uuid = Str::uuid();
                $cart = new Cart();
                $cart->uuid = $uuid;
                $cart->save();
                session()->put('unique_id', $uuid);
            }
        }

        return $cart;
    }
}
