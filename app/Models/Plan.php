<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Plan extends Model
{
    protected $fillable = [
        'name',
        'stripe_product_id',
        'stripe_price_id',
        'price',
        'description',
        'duration_months',
        'downloads',
        'is_recommended',
        'image',
    ];

    protected function casts()
    {
        return [
            'features' => 'array'
        ];
    }

    protected static function booted()
    {
        static::saving(function ($plan) {
            if ($plan->is_recommended) {
                // Desmarca otros planes recomendados
                self::where('is_recommended', true)
                    ->where('id', '!=', $plan->id)
                    ->update(['is_recommended' => false]);
            }
        });
    }

    public function planActive($query)
    {
        return $query->orderBy('price', 'asc');
    }

    /**
     * Scope para obtener el plan recomendado.
     */
    public function planRecommended($query)
    {
        return $query->where('is_recommended', true);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function getPriceFormattedAttribute(): string
    {
        return number_format($this->price, 2);
    }
    protected function image(): Attribute
    {

        $isFrontend = request()->input('is_frontend');

        return Attribute::make(
            get: fn($item) => $item && $isFrontend ? Storage::disk('s3')->url($item) : $item
        );
    }

}
