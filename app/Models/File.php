<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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
        'status',
        'sections',
        'download_count',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'sections' => 'array',
        ];
    }

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
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_files', 'file_id', 'category_id');
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
            get: function ($value) {

                // ✅ En Filament/Livewire devuelve SIEMPRE el key (path) para que funcione preview/remove
                if (request()->is('admin/*') || request()->is('filament/*') || request()->is('livewire/*')) {
                    return $value;
                }

                // ✅ Solo frontend: devuelve URL si viene el flag
                $isFrontend = request()->boolean('is_frontend');

                return ($value && $isFrontend)
                    ? Storage::disk('s3')->url($value)
                    : $value;
            }
        );
    }
    public function monthlyEarning()
    {
        $total = 0;
        $sales = $this->sales()->whereMonth('created_at', Carbon::now()->month)->get();
        foreach ($sales as $sale) {
            $total += $sale->user_amount;
        }
        return $total;
    }
    public function totalEarning()
    {
        $total = 0;
        $sales = $this->sales;
        foreach ($sales as $sale) {
            $total += $sale->user_amount;
        }
        return $total;
    }

    public function getDownloadsEarnings()
    {
        $totalEarning = 0;
        $users = User::all();
        foreach ($users as $user) {
            $totalEarning+=$user->getFileDownloadsEarnings($this->id);
        }
        return $totalEarning;
    }
}
