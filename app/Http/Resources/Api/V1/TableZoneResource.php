<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TableZoneResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'branch_id'    => $this->branch_id,
            'name'         => $this->name,
            'description'  => $this->description,
            'status'       => $this->status,
            'sort_order'   => $this->sort_order,
            'tables_count' => $this->whenCounted('tables'),
            'tables'       => TableResource::collection($this->whenLoaded('tables')),
            'created_at'   => $this->created_at?->toIso8601String(),
        ];
    }
}
