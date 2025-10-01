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
        'is_admin',
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
        return $this->hasActivePlan() ? Carbon::parse($this->plan_expires_at)->diffInDays(Carbon::now()) : 0;
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

}

