<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Branch extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
        'name',
        'address',
        'city',
        'state',
        'cat_mh_departamento_id',
        'cat_mh_municipio_id',
        'phone',
        'email',
        'latitude',
        'longitude',
        'is_default',
        'status',
        'settings',
    ];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
            'latitude'   => 'decimal:7',
            'longitude'  => 'decimal:7',
            'settings'   => 'array',
        ];
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function inventory(): HasMany
    {
        return $this->hasMany(Inventory::class);
    }

    public function tables(): HasMany
    {
        return $this->hasMany(Table::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function departamento(): BelongsTo
    {
        return $this->belongsTo(CatMhDepartamento::class, 'cat_mh_departamento_id');
    }

    public function municipio(): BelongsTo
    {
        return $this->belongsTo(CatMhMunicipio::class, 'cat_mh_municipio_id');
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
