<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
        'product_category_id',
        'cat_mh_unidad_de_medida_id',
        'codigo',
        'nombre',
        'descripcion',
        'precio',
        'peso',
        'tamanio',
        'imagen',
        'status',
        'track_stock',
        'atributos',
    ];

    protected function casts(): array
    {
        return [
            'precio'      => 'decimal:2',
            'peso'        => 'decimal:3',
            'track_stock' => 'boolean',
            'atributos'   => 'array',
        ];
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }

    public function unidadDeMedida(): BelongsTo
    {
        return $this->belongsTo(CatMhUnidadDeMedida::class, 'cat_mh_unidad_de_medida_id');
    }

    public function inventory(): HasMany
    {
        return $this->hasMany(Inventory::class);
    }

    public function changeLogs(): HasMany
    {
        return $this->hasMany(ProductChangeLog::class);
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Registra un cambio en el log del producto.
     */
    public function logChange(string $campo, mixed $valorAnterior, mixed $valorNuevo, ?int $userId = null, ?string $motivo = null): ProductChangeLog
    {
        return $this->changeLogs()->create([
            'user_id'        => $userId,
            'campo'          => $campo,
            'valor_anterior' => is_array($valorAnterior) ? json_encode($valorAnterior) : $valorAnterior,
            'valor_nuevo'    => is_array($valorNuevo) ? json_encode($valorNuevo) : $valorNuevo,
            'motivo'         => $motivo,
        ]);
    }
}
