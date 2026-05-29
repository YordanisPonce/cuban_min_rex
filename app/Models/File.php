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
        'musical_note',
        'isExclusive',
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

    public function getPosterUrl(){
        return ($this->poster)
                    ? Storage::disk('s3')->url($this->poster)
                    : null;
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

    public function scopeVideos($query)
    {
        $ext = ['.mp4', '.avi', '.mov', '.wmv', '.mkv'];

        return $query->where(function ($q) use ($ext) {
            foreach ($ext as $e) {
                $q->orWhere('original_file', 'like', "%{$e}");
            }
        });
    }

    public function scopeAudios($query)
    {
        $ext =  ['.mp3', '.wav', '.ogg', '.m4a', '.flac'];

        return $query->where(function ($q) use ($ext) {
            foreach ($ext as $e) {
                $q->orWhere('original_file', 'like', "%{$e}");
            }
        });
    }

    public function scopeZips($query)
    {
        $ext = ['.zip', '.rar', '.7z'];

        return $query->where(function ($q) use ($ext) {
            foreach ($ext as $e) {
                $q->orWhere('original_file', 'like', "%{$e}");
            }
        });
    }

    public function scopeSection($query, $section)
    {
        return $query->whereJsonContains('sections', $section);
    }

    public function scopeSearch($query, array $words, bool $full_search = false){
        return $query->where(function ($q) use ($words, $full_search) {
            foreach ($words as $word) {
                $full_search
                    ? $q->where('name', 'LIKE', '%' . $word . '%')
                    : $q->orWhere('name', 'LIKE', '%' . $word . '%');
            }
        });
    }
    
    public function intro(){
        return Storage::disk('s3')->url($this->file);
    }

    /**
     * Verify if the file has in current user cart
     * 
     * @return bool
     */
    public function isInCart(): bool
    {
        $cart = Cart::get_current_cart();
        $cartItem = $cart->cart_items()->where('file_id', $this->id)->first();
        return $cartItem !== null;
    }

    public function getExtension()
    {
        return pathinfo($this->original_file, PATHINFO_EXTENSION);
    }

    public function getSize(){
        $size = Storage::disk('s3')->size($this->file);
        if ($size >= 1073741824) {
            return number_format($size / 1073741824, 2) . ' GB';
        } elseif ($size >= 1048576) {
            return number_format($size / 1048576, 2) . ' MB';
        } elseif ($size >= 1024) {
            return number_format($size / 1024, 2) . ' KB';
        } else {
            return $size . ' B';
        }
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
            if($user->role === 'admin'){
                return true;
            }

            $lastPlan = Plan::orderBy('price', 'desc')->first();

            if($user->hasActivePlan() && $user->current_plan_id){

                if($user->plan_start_at){
                    if ($this->isExclusive) {
                        return $user->get_current_plan_consume_downloads() < $user->currentPlan->downloads && $user->current_plan_id === $lastPlan->id;
                    }
                    return $user->get_current_plan_consume_downloads() < $user->currentPlan->downloads;
                }

                return $this->isExclusive ? false : true;
            }
        }
        return false;
    }
}
