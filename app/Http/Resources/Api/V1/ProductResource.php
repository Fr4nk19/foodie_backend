<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                         => $this->id,
            'company_id'                 => $this->company_id,
            'product_category_id'        => $this->product_category_id,
            'cat_mh_unidad_de_medida_id' => $this->cat_mh_unidad_de_medida_id,
            'codigo'                     => $this->codigo,
            'nombre'                     => $this->nombre,
            'descripcion'                => $this->descripcion,
            'precio'                     => $this->precio,
            'peso'                       => $this->peso,
            'tamanio'                    => $this->tamanio,
            'imagen'                     => $this->imagen,
            'status'                     => $this->status,
            'track_stock'                => $this->track_stock,
            'atributos'                  => $this->atributos,
            'categoria'                  => $this->whenLoaded('category', fn () => $this->category ? [
                'id'     => $this->category->id,
                'nombre' => $this->category->nombre,
                'color'  => $this->category->color,
            ] : null),
            'unidad_de_medida'           => $this->whenLoaded('unidadDeMedida', fn () => [
                'id'          => $this->unidadDeMedida->id,
                'codigo'      => $this->unidadDeMedida->codigo,
                'descripcion' => $this->unidadDeMedida->descripcion,
            ]),
            'created_at'                 => $this->created_at?->toIso8601String(),
            'updated_at'                 => $this->updated_at?->toIso8601String(),
        ];
    }
}
