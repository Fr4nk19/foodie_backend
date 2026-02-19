<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CatMhActividadEconomica extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'cat_mh_actividades_economicas';

    protected $fillable = [
        'codigo',
        'descripcion',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function economicActivitiesByCompany(): HasMany
    {
        return $this->hasMany(EconomicActivityByCompany::class, 'cat_mhactividad_id');
    }
}
