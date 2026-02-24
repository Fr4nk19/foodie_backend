<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'product_id' => $this->product_id,
            'quantity'   => $this->quantity,
            'unit_price' => $this->unit_price,
            'subtotal'   => $this->subtotal,
            'notes'      => $this->notes,
            'is_done'    => $this->is_done,
            'product'    => $this->whenLoaded('product', fn () => [
                'id'     => $this->product->id,
                'nombre' => $this->product->nombre,
                'precio' => $this->product->precio,
                'imagen' => $this->product->imagen,
            ]),
        ];
    }
}
