<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyEconomicActivityResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'company_id'       => $this->company_id,
            'cat_mhactividad_id' => $this->cat_mhactividad_id,
            'is_primary'       => $this->is_primary,
            'actividad'        => new CatMhActividadEconomicaResource($this->whenLoaded('actividadEconomica')),
            'created_at'       => $this->created_at?->toIso8601String(),
        ];
    }
}
