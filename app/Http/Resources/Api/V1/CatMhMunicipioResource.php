<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CatMhMunicipioResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                     => $this->id,
            'cat_mh_departamento_id' => $this->cat_mh_departamento_id,
            'departamento'           => $this->whenLoaded('departamento', fn () => [
                'id'          => $this->departamento->id,
                'codigo'      => $this->departamento->codigo,
                'descripcion' => $this->departamento->descripcion,
            ]),
            'codigo'                 => $this->codigo,
            'descripcion'            => $this->descripcion,
            'created_at'             => $this->created_at?->toIso8601String(),
            'updated_at'             => $this->updated_at?->toIso8601String(),
        ];
    }
}
