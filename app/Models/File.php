<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;

class File extends Model
{
    protected $fillable = [
        'name',
        'collection_id',
        'category_id',
        'user_id',
        'price',
        'bpm',
        'file',
        'poster',
        'original_file',
        'download_count',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function collection(): BelongsTo
    {
        return $this->belongsTo(Collection::class);
    }
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
    public function downloads()
    {
        return $this->hasMany(Download::class);
    }
    protected function poster(): Attribute
    {

        $isFrontend = request()->input('is_frontend');

        return Attribute::make(
            get: fn($item) => $item && $isFrontend ? Storage::disk('s3')->url($item) : $item
        );
    }
    public function monthlyEarning(){
        $total = 0;
        $sales = $this->sales()->whereMonth('created_at', Carbon::now()->month)->get();
        foreach ($sales as $sale) {
            $total += $sale->user_amount;
        }
        return $total;
    }
    public function totalEarning(){
        $total = 0;
        $sales = $this->sales;
        foreach ($sales as $sale) {
            $total += $sale->user_amount;
        }
        return $total;
    }
}
