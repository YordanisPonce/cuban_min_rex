<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = [
        'name',
        'stripe_product_id',
        'stripe_price_id',
        'price',
        'description',
        'duration_months',
        'is_recommended',
    ];

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

}
