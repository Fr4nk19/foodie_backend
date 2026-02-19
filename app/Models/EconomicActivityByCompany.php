<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class EconomicActivityByCompany extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'economic_activity_by_company';

    protected $fillable = [
        'company_id',
        'cat_mhactividad_id',
        'is_primary',
    ];

    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
        ];
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function actividadEconomica(): BelongsTo
    {
        return $this->belongsTo(CatMhActividadEconomica::class, 'cat_mhactividad_id');
    }
}
