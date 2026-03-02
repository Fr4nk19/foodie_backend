<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inventory extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'inventory';

    protected $fillable = [
        'branch_id',
        'product_id',
        'cantidad',
        'cantidad_minima',
        'cantidad_maxima',
        'costo_unitario',
    ];

    protected function casts(): array
    {
        return [
            'cantidad'        => 'decimal:3',
            'cantidad_minima' => 'decimal:3',
            'cantidad_maxima' => 'decimal:3',
            'costo_unitario'  => 'decimal:2',
        ];
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    public function isBelowMinimum(): bool
    {
        return $this->cantidad_minima !== null && $this->cantidad < $this->cantidad_minima;
    }

    public function isAboveMaximum(): bool
    {
        return $this->cantidad_maxima !== null && $this->cantidad > $this->cantidad_maxima;
    }
}
