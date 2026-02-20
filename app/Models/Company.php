<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'email',
        'phone',
        'logo',
        'address',
        'city',
        'state',
        'country',
        'timezone',
        'status',
        'plan',
        'trial_ends_at',
        'settings',
    ];

    protected function casts(): array
    {
        return [
            'trial_ends_at' => 'datetime',
            'settings'      => 'array',
        ];
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function branches(): HasMany
    {
        return $this->hasMany(Branch::class);
    }

    public function defaultBranch(): HasOne
    {
        return $this->hasOne(Branch::class)->where('is_default', true);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function economicActivities(): HasMany
    {
        return $this->hasMany(EconomicActivityByCompany::class);
    }

    public function primaryEconomicActivity(): HasOne
    {
        return $this->hasOne(EconomicActivityByCompany::class)->where('is_primary', true);
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }
}
