<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductChangeLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'user_id',
        'campo',
        'valor_anterior',
        'valor_nuevo',
        'motivo',
    ];

    // Sin SoftDeletes: el historial nunca se borra

    // ─── Relationships ────────────────────────────────────────────────────────

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
