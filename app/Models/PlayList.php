<?php

namespace App\Models;

use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlayList extends Model
{
    protected $fillable = [
        'cover',
        'name',
        'description',
        'user_id',
        'is_public',
        'folder_id',
        'price'
    ];

    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(PlayListItem::class);
    }

    public function downloads()
    {
        return $this->hasMany(Download::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function folder(): BelongsTo
    {
        return $this->belongsTo(Folder::class);
    }

    /**
     * Get the playlist items file paths
     * 
     * @return array
     */
    public function getPlayList(): array
    {
        $paths = $this->items()->get()->pluck('file_path')->toArray();

        $paths = array_map(function ($path) {
            return Storage::disk('s3')->url($path ?? '');
        }, $paths);
        
        return $paths;
    }
    
    public function getCoverUrl()
    {
        return Storage::disk('s3')->url($this->cover);
    }

    /**
     * Verify if the playlist has in current user cart
     * 
     * @return bool
     */
    public function isInCart(): bool
    {
        $cart = Cart::get_current_cart();
        $cartItem = $cart->cart_items()->where('play_list_id', $this->id)->first();
        
        return $cartItem !== null;
    
    }

    /**
     * Verify if the current auth user can download the resource
     * 
     * @return bool
     */
    public function canBeDownload() : bool
    {
        $user = auth()->check() ? auth()->user() : null;
        if($user){
            $firstPlan = Plan::orderBy('price')->first();

            if($user->hasActivePlan() && $user->current_plan_id){
                if($user->plan_start_at){
                    return $user->get_current_plan_consume_downloads() < $user->plan->downloads && $user->current_plan_id != $firstPlan->id;
                }
                return $user->current_plan_id != $firstPlan->id;
            }
        }
        return false;
    }
}