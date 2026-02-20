<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CatMhUnidadDeMedida extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'cat_mh_unidades_de_medida';

    protected $fillable = [
        'codigo',
        'descripcion',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'cat_mh_unidad_de_medida_id');
    }
}
