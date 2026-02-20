<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'slug'          => $this->slug,
            'email'         => $this->email,
            'phone'         => $this->phone,
            'logo'          => $this->logo,
            'address'       => $this->address,
            'city'          => $this->city,
            'state'         => $this->state,
            'cat_mh_departamento_id' => $this->cat_mh_departamento_id,
            'cat_mh_municipio_id'    => $this->cat_mh_municipio_id,
            'departamento'  => $this->whenLoaded('departamento', fn () => [
                'id'          => $this->departamento->id,
                'codigo'      => $this->departamento->codigo,
                'descripcion' => $this->departamento->descripcion,
            ]),
            'municipio'     => $this->whenLoaded('municipio', fn () => [
                'id'          => $this->municipio->id,
                'codigo'      => $this->municipio->codigo,
                'descripcion' => $this->municipio->descripcion,
            ]),
            'country'       => $this->country,
            'timezone'      => $this->timezone,
            'status'        => $this->status,
            'plan'          => $this->plan,
            'trial_ends_at' => $this->trial_ends_at?->toIso8601String(),
            'branches'      => BranchResource::collection($this->whenLoaded('branches')),
            'default_branch'=> new BranchResource($this->whenLoaded('defaultBranch')),
            'created_at'    => $this->created_at?->toIso8601String(),
        ];
    }
}
