<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InventoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'branch_id'       => $this->branch_id,
            'product_id'      => $this->product_id,
            'cantidad'        => $this->cantidad,
            'cantidad_minima' => $this->cantidad_minima,
            'cantidad_maxima' => $this->cantidad_maxima,
            'costo_unitario'  => $this->costo_unitario,
            'product'         => $this->whenLoaded('product', fn () => [
                'id'               => $this->product->id,
                'codigo'           => $this->product->codigo,
                'nombre'           => $this->product->nombre,
                'descripcion'      => $this->product->descripcion,
                'precio'           => $this->product->precio,
                'status'           => $this->product->status,
                'track_stock'      => $this->product->track_stock,
                'unidad_de_medida' => $this->product->relationLoaded('unidadDeMedida') ? [
                    'id'          => $this->product->unidadDeMedida->id,
                    'codigo'      => $this->product->unidadDeMedida->codigo,
                    'descripcion' => $this->product->unidadDeMedida->descripcion,
                ] : null,
            ]),
            'created_at'      => $this->created_at?->toIso8601String(),
            'updated_at'      => $this->updated_at?->toIso8601String(),
        ];
    }
}
