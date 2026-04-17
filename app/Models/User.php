<?php

namespace App\Models;

use Carbon\Carbon;
use DateTime;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Storage;
use Stripe\Stripe;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable, Billable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'photo',
        'cover',
        'role',
        'is_admin',
        'paypal_email',
        'email_verified_at',
        'downloadToken',
        'current_plan_id',
        'plan_start_at',
        'plan_expires_at',
        'bio',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'downloadToken' => 'array',
        ];
    }

    // // === Filament ===
    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    public function getFilamentName(): string
    {
        return $this->name;
    }

    protected function photo(): Attribute
    {

        $isFrontend = request()->input('is_frontend');

        return Attribute::make(
            get: fn($item) => $item && $isFrontend ? Storage::disk('s3')->url($item) : $item
        );
    }

    protected function cover(): Attribute
    {

        $isFrontend = request()->input('is_frontend');

        return Attribute::make(
            get: fn($item) => $item && $isFrontend ? Storage::disk('s3')->url($item) : $item
        );
    }

    // === Socialite ===
    public function getSocialiteAvatarUrl(): ?string
    {
        return $this->avatar_url ?? null;
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function currentPlan(): BelongsTo
    {
        return $this->belongsTo(Plan::class, 'current_plan_id');
    }

    public function hasActivePlan(): bool
    {
        $isFuture = false;
        if ($this->plan_expires_at) {
            $expirationDate = new DateTime($this->plan_expires_at);
            if ($expirationDate > new DateTime()) {
                $isFuture = true;
            }
        }
        return $isFuture;
    }

    public function planExpirationDays()
    {
        return (object) [
            'days' => $this->hasActivePlan() ? Carbon::parse($this->plan_expires_at)->diff(Carbon::now())->days : 0
        ];
    }

    public function billing()
    {
        return $this->hasOne(Billing::class);
    }

    public function socialLinks()
    {
        return $this->hasOne(SocialLink::class);
    }

    public function cart()
    {
        return $this->hasOne(Cart::class);
    }

    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    public function collections()
    {
        return $this->hasMany(Collection::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function files()
    {
        return $this->hasMany(File::class);
    }

    public function downloads()
    {
        return $this->hasMany(Download::class);
    }

    public function playlists()
    {
        return $this->hasMany(PlayList::class);
    }
    
    public function follows(): HasMany
    {
        return $this->hasMany(Follow::class, 'follower_id');
    }
    
    public function followers(): HasMany
    {
        return $this->hasMany(Follow::class, 'follow_id');
    }
    
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'dj_id');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'user_id');
    }

    public function ntfs_prefs(): HasOne
    {
        return $this->hasOne(NotificationSettings::class, 'user_id');
    }

    public function totalUnliquidatedDownloads(): int
    {
        return (int) Download::query()
            ->join('files', 'downloads.file_id', '=', 'files.id')
            ->where('downloads.liquidated', false)
            ->where('files.user_id', $this->id) // DJ dueño
            ->selectRaw('COUNT(DISTINCT downloads.user_id, downloads.file_id) as cnt')
            ->value('cnt');
    }

    public function pendingSubscriptionLiquidation(): float
    {
        // Pool pendiente (solo suscripciones pagadas NO repartidas aún)
        $grossPending = (float) Order::query()
            ->where('status', 'paid')
            ->whereNotNull('plan_id')
            ->whereNull('settled_at')
            ->sum('amount');

        $poolPending = $grossPending * 0.70;
        if ($poolPending <= 0) {
            return 0.0;
        }

        /*
        |--------------------------------------------------------------------------
        | 1. TOTAL GLOBAL: pares únicos (user_id, elemento) sin liquidar
        |--------------------------------------------------------------------------
        */
        $totalPendingPairs = Download::query()
            ->where('liquidated', false)
            ->selectRaw("
                COUNT(DISTINCT 
                    user_id,
                    COALESCE(file_id, play_list_id, play_list_item_id),
                    CASE
                        WHEN file_id IS NOT NULL THEN 'file'
                        WHEN play_list_id IS NOT NULL THEN 'playlist'
                        WHEN play_list_item_id IS NOT NULL THEN 'playlist_item'
                    END
                ) AS cnt
            ")
            ->value('cnt');

        if ($totalPendingPairs === 0) {
            return 0.0;
        }

        /*
        |--------------------------------------------------------------------------
        | 2. PARES DEL DJ: elementos cuyo owner es este usuario ($this->id)
        |--------------------------------------------------------------------------
        |
        | IMPORTANTE:
        | - play_list_items no tiene user_id → se une a play_lists
        | - play_lists sí tiene user_id
        | - files tiene user_id
        */
        $djPendingPairs = Download::query()
            ->where('downloads.liquidated', false)
            ->leftJoin('files', 'downloads.file_id', '=', 'files.id')
            ->leftJoin('play_lists', 'downloads.play_list_id', '=', 'play_lists.id')
            ->leftJoin('play_list_items', 'downloads.play_list_item_id', '=', 'play_list_items.id')
            ->leftJoin('play_lists as pli_parent', 'play_list_items.play_list_id', '=', 'pli_parent.id')
            ->where(function ($q) {
                $q->where('files.user_id', $this->id)               // files del DJ
                ->orWhere('play_lists.user_id', $this->id)        // playlists del DJ
                ->orWhere('pli_parent.user_id', $this->id);       // playlist_items del DJ
            })
            ->selectRaw("
                COUNT(DISTINCT 
                    downloads.user_id,
                    COALESCE(downloads.file_id, downloads.play_list_id, downloads.play_list_item_id),
                    CASE
                        WHEN downloads.file_id IS NOT NULL THEN 'file'
                        WHEN downloads.play_list_id IS NOT NULL THEN 'playlist'
                        WHEN downloads.play_list_item_id IS NOT NULL THEN 'playlist_item'
                    END
                ) AS cnt
            ")
            ->value('cnt');

        /*
        |--------------------------------------------------------------------------
        | 3. Proporción del pool
        |--------------------------------------------------------------------------
        */
        $amount = $poolPending * ($djPendingPairs / $totalPendingPairs);

        return round($amount, 2);
    }

    public function paidSubscriptionLiquidation(): float
    {
        // Pool pendiente (solo suscripciones pagadas NO repartidas aún)
        $grossPaid = (float) Order::query()
            ->where('status', 'paid')
            ->whereNotNull('plan_id')
            ->whereNotNull('settled_at')
            ->sum('amount');

        $poolPaid = $grossPaid * 0.70;
        if ($poolPaid <= 0) {
            return 0.0;
        }

        /*
        |--------------------------------------------------------------------------
        | 1. TOTAL GLOBAL: pares únicos (user_id, elemento) liquidados
        |--------------------------------------------------------------------------
        */
        $totalPaidPairs = Download::query()
            ->where('liquidated', true)
            ->selectRaw("
                COUNT(DISTINCT 
                    user_id,
                    COALESCE(file_id, play_list_id, play_list_item_id),
                    CASE
                        WHEN file_id IS NOT NULL THEN 'file'
                        WHEN play_list_id IS NOT NULL THEN 'playlist'
                        WHEN play_list_item_id IS NOT NULL THEN 'playlist_item'
                    END
                ) AS cnt
            ")
            ->value('cnt');

        if ($totalPaidPairs === 0) {
            return 0.0;
        }

        /*
        |--------------------------------------------------------------------------
        | 2. PARES DEL DJ: elementos cuyo owner es este usuario ($this->id)
        |--------------------------------------------------------------------------
        |
        | IMPORTANTE:
        | - play_list_items no tiene user_id → se une a play_lists
        | - play_lists sí tiene user_id
        | - files tiene user_id
        */
        $djPaidPairs = Download::query()
            ->where('downloads.liquidated', true)
            ->leftJoin('files', 'downloads.file_id', '=', 'files.id')
            ->leftJoin('play_lists', 'downloads.play_list_id', '=', 'play_lists.id')
            ->leftJoin('play_list_items', 'downloads.play_list_item_id', '=', 'play_list_items.id')
            ->leftJoin('play_lists as pli_parent', 'play_list_items.play_list_id', '=', 'pli_parent.id')
            ->where(function ($q) {
                $q->where('files.user_id', $this->id)               // files del DJ
                ->orWhere('play_lists.user_id', $this->id)        // playlists del DJ
                ->orWhere('pli_parent.user_id', $this->id);       // playlist_items del DJ
            })
            ->selectRaw("
                COUNT(DISTINCT 
                    downloads.user_id,
                    COALESCE(downloads.file_id, downloads.play_list_id, downloads.play_list_item_id),
                    CASE
                        WHEN downloads.file_id IS NOT NULL THEN 'file'
                        WHEN downloads.play_list_id IS NOT NULL THEN 'playlist'
                        WHEN downloads.play_list_item_id IS NOT NULL THEN 'playlist_item'
                    END
                ) AS cnt
            ")
            ->value('cnt');

        /*
        |--------------------------------------------------------------------------
        | 3. Proporción del pool
        |--------------------------------------------------------------------------
        */
        $amount = $poolPaid * ($djPaidPairs / $totalPaidPairs);

        return round($amount, 2);
    }

    public function generatedToSubscriptionLiquidation()
    {
        // Pool pendiente (solo suscripciones pagadas NO repartidas aún)
        $grossPaid = (float) Order::query()
            ->where('status', 'paid')
            ->whereNotNull('plan_id')
            ->whereNull('settled_at')
            ->sum('amount');

        $poolPaid = $grossPaid * 0.10;
        if ($poolPaid <= 0) {
            return 0.0;
        }

        /*
        |--------------------------------------------------------------------------
        | 1. TOTAL GLOBAL: pares únicos (user_id, elemento) liquidados
        |--------------------------------------------------------------------------
        */
        $totalPaidPairs = Download::query()
            ->where('liquidated', true)
            ->selectRaw("
                COUNT(DISTINCT 
                    user_id,
                    COALESCE(file_id, play_list_id, play_list_item_id),
                    CASE
                        WHEN file_id IS NOT NULL THEN 'file'
                        WHEN play_list_id IS NOT NULL THEN 'playlist'
                        WHEN play_list_item_id IS NOT NULL THEN 'playlist_item'
                    END
                ) AS cnt
            ")
            ->value('cnt');

        if ($totalPaidPairs === 0) {
            return 0.0;
        }

        /*
        |--------------------------------------------------------------------------
        | 2. PARES DEL DJ: elementos cuyo owner es este usuario ($this->id)
        |--------------------------------------------------------------------------
        |
        | IMPORTANTE:
        | - play_list_items no tiene user_id → se une a play_lists
        | - play_lists sí tiene user_id
        | - files tiene user_id
        */
        $djPaidPairs = Download::query()
            ->where('downloads.liquidated', true)
            ->leftJoin('files', 'downloads.file_id', '=', 'files.id')
            ->leftJoin('play_lists', 'downloads.play_list_id', '=', 'play_lists.id')
            ->leftJoin('play_list_items', 'downloads.play_list_item_id', '=', 'play_list_items.id')
            ->leftJoin('play_lists as pli_parent', 'play_list_items.play_list_id', '=', 'pli_parent.id')
            ->where(function ($q) {
                $q->where('files.user_id', $this->id)               // files del DJ
                ->orWhere('play_lists.user_id', $this->id)        // playlists del DJ
                ->orWhere('pli_parent.user_id', $this->id);       // playlist_items del DJ
            })
            ->selectRaw("
                COUNT(DISTINCT 
                    downloads.user_id,
                    COALESCE(downloads.file_id, downloads.play_list_id, downloads.play_list_item_id),
                    CASE
                        WHEN downloads.file_id IS NOT NULL THEN 'file'
                        WHEN downloads.play_list_id IS NOT NULL THEN 'playlist'
                        WHEN downloads.play_list_item_id IS NOT NULL THEN 'playlist_item'
                    END
                ) AS cnt
            ")
            ->value('cnt');

        /*
        |--------------------------------------------------------------------------
        | 3. Proporción del pool
        |--------------------------------------------------------------------------
        */
        $amount = $poolPaid * ($djPaidPairs / $totalPaidPairs);

        return round($amount, 2);
    }

    public function pendingSaleLiquidation()
    {
        return Sale::where('status', 'pending')->whereHas('file', function ($query) {
            $query->where('user_id', $this->id);
        })->orWhereHas('playlist', function ($query) {
            $query->where('user_id', $this->id);
        })->orWhereHas('playlistItem', function ($query) {
            $query->whereHas('playList', function ($query) {
                $query->where('user_id', $this->id);
            });
        })->sum('user_amount');
    }

    public function paidSaleLiquidation()
    {
        return Sale::where('status', 'paid')->whereHas('file', function ($query) {
            $query->where('user_id', $this->id);
        })->orWhereHas('playlist', function ($query) {
            $query->where('user_id', $this->id);
        })->orWhereHas('playlistItem', function ($query) {
            $query->whereHas('playList', function ($query) {
                $query->where('user_id', $this->id);
            });
        })->sum('user_amount');
    }

    public function generatedToSaleLiquidation()
    {
        return Sale::where('status', 'paid')->whereHas('file', function ($query) {
            $query->where('user_id', $this->id);
        })->orWhereHas('playlist', function ($query) {
            $query->where('user_id', $this->id);
        })->orWhereHas('playlistItem', function ($query) {
            $query->whereHas('playList', function ($query) {
                $query->where('user_id', $this->id);
            });
        })->sum('admin_amount');
    }

    public function getCurrentMonthDownloads()
    {
        return $this->downloads()->whereMonth('created_at', Carbon::now()->month)->count();
    }

    public function getFileCurrentMonthDownloads($fileId)
    {
        return $this->downloads()->where('file_id', $fileId)->whereMonth('created_at', Carbon::now()->month)->count();
    }

    public function getFileDownloads($fileId)
    {
        return $this->downloads()->where('file_id', $fileId)->count();
    }

    public function totalEarning()
    {
        $totalPaid = 0;
        $users = User::all();
        foreach ($users as $user) {
            $plan = null;

            if ($user->hasActivePlan() && $user->currentPlan()) {
                $plan = Plan::find($user->current_plan_id);
            } else {
                $order = Order::where('user_id', $user->id)->orderBy('created_at', 'desc')->first();
                if ($order) {
                    if (Carbon::parse($order->expires_at)->isFuture() || Carbon::parse($order->expires_at)->month === Carbon::now()->month) {
                        $plan = $order?->plan;
                    }
                }
            }

            if ($plan) {
                $planAmount = $plan->price / $plan->duration_months * 0.7;
                $downloads = count($user->downloads);
                $downloadsToDJ = Download::whereHas('file', function ($query) {
                    $query->where('user_id', $this->id)->where('liquidated', true);
                })
                    ->where('user_id', $user->id)
                    ->distinct('file_id')
                    ->count('file_id');
                if ($downloads == 0) {
                    $amountToPay = 0;
                } else {
                    $amountToPay = $planAmount * ($downloadsToDJ / $downloads);
                }

                $totalPaid += $amountToPay;
            }
        }
        return $totalPaid + $this->paidSaleLiquidation();
    }

    public function get_current_plan_consume_downloads(){
        return $this->downloads()->whereBetween('created_at', [$this->plan_start_at, $this->plan_expires_at])->count();
    }

    public function getFileDownloadsAtSubscriptionPeriod($fileId)
    {
        Stripe::setApiKey(config('services.stripe.secret_key'));

        $customerId = $this->stripe_id;

        $subscriptions = \Stripe\Subscription::all(['customer' => $customerId]);

        $subscriptions = array_filter($subscriptions->data, function ($subscription) {
            return $subscription->status === 'active';
        });

        if (!empty($subscriptions->data)) {

            $stripeSubscription = $subscriptions[0];

            $start_date = \Carbon\Carbon::createFromTimestamp($stripeSubscription->start_date);
            $end_date = \Carbon\Carbon::createFromTimestamp($stripeSubscription->current_period_end);

            return $this->downloads()->where('file_id', $fileId)->whereBetween('created_at', [$start_date, $end_date])->count();
        }

        return $this->downloads()->where('file_id', $fileId)->count();
    }

    public function getFileDownloadsEarnings($fileId)
    {
        $totalEarning = 0;
        $downloads = $this->downloads()->whereHas('file', function ($query) use ($fileId) {
            $query->where('file_id', $fileId);
        });
        foreach ($downloads as $download) {
            $dateX = Carbon::parse($download->create_at);
            $order = Order::whereHas('plan')
                ->where('created_at', '<', $dateX)
                ->where('expires_at', '>', $dateX)
                ->orderBy('create_at')
                ->first();
            if ($order) {
                $plan = $order->plan;

                $start_date = \Carbon\Carbon::createFromTimestamp($order->create_at);
                $end_date = \Carbon\Carbon::createFromTimestamp($order->expires_at);

                if ($plan) {
                    $planAmount = $plan->price / $plan->duration_months * 0.7;

                    $fileDownloads = $this->downloads()->whereHas('file', function ($query) use ($fileId) {
                        $query->where('file_id', $fileId);
                    })->whereBetween('created_at', [$start_date, $end_date])->count();

                    $totalDownloads = $this->downloads()->whereBetween('created_at', [$start_date, $end_date])->count();

                    if ($downloads == 0) {
                        $amountEarning = 0;
                    } else {
                        $amountEarning = $planAmount * ($fileDownloads / $totalDownloads);
                    }

                    $totalEarning += $amountEarning;
                }
            }
        }
        return $totalEarning;
    }

    public function pendingSalesTotal(): float
    {
        return (float) Sale::query()
            ->where('status', 'pending')
            ->whereHas('file', fn($q) => $q->where('user_id', $this->id))
            ->sum('user_amount');
    }

    public function paidSalesTotal(): float
    {
        return (float) Sale::query()
            ->where('status', 'paid')
            ->whereHas('file', fn($q) => $q->where('user_id', $this->id))
            ->sum('user_amount');
    }

    public function isAdmin()
    {
        return $this->role == 'admin';
    }

    public function recordAdmin($query){
        return $query->where('role', 'admin');
    }

    /**
     * Get the Downloads to Dj Files without repeat files
     * 
     * @param int $djId -> id of the DJ
     * 
     * @return int downloads count
     */
    public function getDistinctDownloadsTo(int $djId) : int {
        return Download::where('user_id', $this->id) ->whereHas('file', function($query) use ($djId) { $query->where('user_id', $djId); }) ->distinct('file_id')->count('file_id');
    }

    /**
     * Get the Downloads without repeat files
     * 
     * @return int downloads count
     */
    public function getDistinctDownloads(): int {
        return $this->downloads()->distinct('file_id')->count('file_id');
    }

    /**
     * Get the Downloads recived without repeat files by a same user
     * 
     * @return int downloads count
     */
    function getDistinctDownloadsRecived() : int {
        $cont = 0;
        foreach (User::where('role', 'user')->get() as $user) {
            $cont += $user->getDistinctDownloadsTo($this->id);
        }
        return $cont;
    }

    /**
     * Get the number of Pending Sales
     */
    function pendingSalesCount() : int {
        /*
        |--------------------------------------------------------------------------
        | VENTAS PENDIENTES DEL DJ
        |--------------------------------------------------------------------------
        |
        | Sale tiene file_id, play_list_id y play_list_item_id igual que Download.
        | Debemos unir correctamente para saber si el elemento pertenece al DJ.
        */
        $sales = Sale::query()
            ->where('sales.status', 'pending')
            ->leftJoin('files', 'sales.file_id', '=', 'files.id')
            ->leftJoin('play_lists', 'sales.play_list_id', '=', 'play_lists.id')
            ->leftJoin('play_list_items', 'sales.play_list_item_id', '=', 'play_list_items.id')
            ->leftJoin('play_lists as pli_parent', 'play_list_items.play_list_id', '=', 'pli_parent.id')
            ->where(function ($q) {
                $q->where('files.user_id', $this->id)
                ->orWhere('play_lists.user_id', $this->id)
                ->orWhere('pli_parent.user_id', $this->id);
            })
            ->selectRaw("
                COUNT(DISTINCT 
                    COALESCE(sales.file_id, sales.play_list_id, sales.play_list_item_id),
                    CASE
                        WHEN sales.file_id IS NOT NULL THEN 'file'
                        WHEN sales.play_list_id IS NOT NULL THEN 'playlist'
                        WHEN sales.play_list_item_id IS NOT NULL THEN 'playlist_item'
                    END
                ) AS cnt
            ")
            ->value('cnt');

        return $sales;
    }

    /**
     * Get the number of Downloads without liquidated
     */
    function pendingDownloadsCount() : int {
        /*
        |--------------------------------------------------------------------------
        | DESCARGAS PENDIENTES DEL DJ
        |--------------------------------------------------------------------------
        |
        | Igual que antes, pero usando Download.
        */
        $downloads = Download::query()
            ->where('downloads.liquidated', false)
            ->leftJoin('files', 'downloads.file_id', '=', 'files.id')
            ->leftJoin('play_lists', 'downloads.play_list_id', '=', 'play_lists.id')
            ->leftJoin('play_list_items', 'downloads.play_list_item_id', '=', 'play_list_items.id')
            ->leftJoin('play_lists as pli_parent', 'play_list_items.play_list_id', '=', 'pli_parent.id')
            ->where(function ($q) {
                $q->where('files.user_id', $this->id)
                ->orWhere('play_lists.user_id', $this->id)
                ->orWhere('pli_parent.user_id', $this->id);
            })
            ->selectRaw("
                COUNT(DISTINCT 
                    downloads.user_id,
                    COALESCE(downloads.file_id, downloads.play_list_id, downloads.play_list_item_id),
                    CASE
                        WHEN downloads.file_id IS NOT NULL THEN 'file'
                        WHEN downloads.play_list_id IS NOT NULL THEN 'playlist'
                        WHEN downloads.play_list_item_id IS NOT NULL THEN 'playlist_item'
                    END
                ) AS cnt
            ")
            ->value('cnt');

        return $downloads;
    }

    /**
     * Get the number of Downloads + Sales without liquidated
     * 
     * @return int Downloads + Sales Count
     */
    function getPendingSalesCount() : int 
    {
        $sales = $this->pendingSalesCount();
        $downloads = $this->pendingDownloadsCount();
        return $sales + $downloads;
    }


    /**
     * Get the Pending Sales Query
     * 
     * @return \Illuminate\Database\Eloquent\Builder Pending sale query
     */

    function pendingSales(): \Illuminate\Database\Eloquent\Builder {
        $query = Sale::query()
            ->where('status', 'pending')
            ->whereHas('file', fn($q) => $q->where('user_id', $this->id));

        return $query;
    }

    /**
     * Get the Pending Downloads Query
     * 
     * @return \Illuminate\Database\Eloquent\Builder Pending sale query
     */
    function pendingDownloads(): \Illuminate\Database\Eloquent\Builder {
        $query = Download::query()
            ->where('liquidated', false)
            ->distinct('user_id','file_id')
            ->whereHas('file', fn($q) => $q->where('user_id', $this->id));

        return $query;
    }

    /**
     * Get the total amount of pending commission for the developer (CubanPool)
     * 
     * @return float
     */
    static function getDevUnpaidCommition(): float
    {
        return static::getGeneratedAmount() * 0.2;
    }

    /**
     * Get the total amount generated by the platform that is pending to be paid to DJs (70%), Developer (20%) and CubanPool (10%)
     */
    static function getGeneratedAmount(): float
    {
        $subscriptions = (float) Order::query()
            ->where('status', 'paid')
            ->whereNotNull('plan_id')
            ->whereNull('settled_at')
            ->sum('amount');

        $sales = (float)  Sale::query()
            ->where('status', 'pending')
            ->sum('amount');

        return $subscriptions + $sales;
    }
}
