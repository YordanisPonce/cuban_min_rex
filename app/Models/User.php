<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;


class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable, Billable;

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
        return $this->role==='admin'; // todos los usuarios pueden acceder, hay que cambiar para Pro
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

    public function orders(): HasMany { return $this->hasMany(Order::class); }

    public function currentPlan(): BelongsTo {
        return $this->belongsTo(Plan::class, 'current_plan_id');
    }

    public function hasActivePlan(): bool {
        return !is_null($this->current_plan_id) && $this->plan_expires_at?->isFuture();
    }

    public function billing(){
        return $this->hasOne(Billing::class);
    }




}

