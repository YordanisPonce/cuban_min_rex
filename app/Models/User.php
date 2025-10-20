<?php

namespace App\Models;

use Carbon\Carbon;
use DateInterval;
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
        'avatar_url',
        'role',
        'is_admin',
        'paypal_email',
        'email_verified_at',
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

    public function totalUnliquidatedDownloads()
    {
        $totalUnliquidatedDownloads = $this->files()->with([
            'downloads' => function ($query) {
                $query->where('liquidated', false)->whereMonth('created_at', Carbon::now()->month);
            }
        ])->get()->sum(function ($file) {
            return $file->downloads->count();
        });
        return $totalUnliquidatedDownloads;
    }

    public function pendingSubscriptionLiquidation()
    {
        $pendingToPay = 0;
        $users = User::all();
        foreach ($users as $user) {
            if ($user->hasActivePlan() && $user->currentPlan()) {
                $plan = Plan::find($user->current_plan_id);
                $planAmount = $plan->price / $plan->duration_months * 0.7;
                $downloads = $user->downloads()->whereMonth('created_at', Carbon::now()->month)->count();
                $downloadsToDJ = Download::whereHas('file', callback: function ($query) {
                    $query->where('user_id', $this->id)->where('liquidated', false);
                })
                    ->where('user_id', $user->id)
                    ->whereMonth('created_at', Carbon::now()->month)
                    ->distinct('file_id')
                    ->count('file_id');
                if ($downloads == 0) {
                    $amountToPay = 0;
                } else {
                    $amountToPay = $planAmount * ($downloadsToDJ / $downloads);

                }
                $pendingToPay += $amountToPay;
            }
        }
        return $pendingToPay;
    }

    public function paidSubscriptionLiquidation()
    {
        $totalPaid = 0;
        $users = User::all();
        foreach ($users as $user) {
            if ($user->hasActivePlan() && $user->currentPlan()) {
                $plan = Plan::find($user->current_plan_id);
                $planAmount = $plan->price / $plan->duration_months * 0.7;
                $downloads = $user->downloads()->whereMonth('created_at', Carbon::now()->month)->count();
                $downloadsToDJ = Download::whereHas('file', function ($query) {
                    $query->where('user_id', $this->id)->where('liquidated', true);
                })
                    ->where('user_id', $user->id)
                    ->whereMonth('created_at', Carbon::now()->month)
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
        return $totalPaid;
    }

    public function generatedToSubscriptionLiquidation()
    {
        $totalGenerated = 0;
        $users = User::all();
        foreach ($users as $user) {
            if ($user->hasActivePlan() && $user->currentPlan()) {
                $plan = Plan::find($user->current_plan_id);
                $planAmount = $plan->price / $plan->duration_months * 0.3;
                $downloads = $user->downloads()->whereMonth('created_at', Carbon::now()->month)->count();
                $downloadsToDJ = Download::whereHas('file', function ($query) {
                    $query->where('user_id', $this->id)->where('liquidated', true);
                })
                    ->where('user_id', $user->id)
                    ->whereMonth('created_at', Carbon::now()->month)
                    ->distinct('file_id')
                    ->count('file_id');

                if ($downloads == 0) {
                    $amountToPay = 0;
                } else {
                    $amountToPay = $planAmount * ($downloadsToDJ / $downloads);
                }

                $totalGenerated += $amountToPay;
            }
        }
        return $totalGenerated;
    }
    
    public function pendingSaleLiquidation()
    {
        $totalPaid = 0;
        $sales = Sale::whereHas('file', function ($query) {
            $query->where('user_id', $this->id)->where('status', 'pending');
        })->get();
        foreach ($sales as $sale) {
            $totalPaid += $sale->user_amount;
        }
        return $totalPaid;
    }

    public function paidSaleLiquidation()
    {
        $totalPaid = 0;
        $sales = Sale::whereHas('file', function ($query) {
            $query->where('user_id', $this->id)->where('status', 'paid');
        })->get();
        foreach ($sales as $sale) {
            $totalPaid += $sale->user_amount;
        }
        return $totalPaid;
    }

    public function generatedToSaleLiquidation()
    {
        $totalPaid = 0;
        $sales = Sale::whereHas('file', function ($query) {
            $query->where('user_id', $this->id)->where('status', 'paid');
        })->get();
        foreach ($sales as $sale) {
            $totalPaid += $sale->admin_amount;
        }
        return $totalPaid;
    }

    public function getCurrentMonthDownloads() {
        return $this->downloads()->whereMonth('created_at', Carbon::now()->month)->count();
    }
}

