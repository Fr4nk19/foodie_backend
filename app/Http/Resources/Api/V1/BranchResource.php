<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BranchResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'company_id' => $this->company_id,
            'name'       => $this->name,
            'address'    => $this->address,
            'city'       => $this->city,
            'state'      => $this->state,
            'cat_mh_departamento_id' => $this->cat_mh_departamento_id,
            'cat_mh_municipio_id'    => $this->cat_mh_municipio_id,
            'departamento' => $this->whenLoaded('departamento', fn () => [
                'id'          => $this->departamento->id,
                'codigo'      => $this->departamento->codigo,
                'descripcion' => $this->departamento->descripcion,
            ]),
            'municipio'    => $this->whenLoaded('municipio', fn () => [
                'id'          => $this->municipio->id,
                'codigo'      => $this->municipio->codigo,
                'descripcion' => $this->municipio->descripcion,
            ]),
            'phone'      => $this->phone,
            'email'      => $this->email,
            'latitude'   => $this->latitude,
            'longitude'  => $this->longitude,
            'is_default' => $this->is_default,
            'status'     => $this->status,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
