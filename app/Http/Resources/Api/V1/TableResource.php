<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TableResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'branch_id'     => $this->branch_id,
            'table_zone_id' => $this->table_zone_id,
            'number'        => $this->number,
            'capacity'      => $this->capacity,
            'zone'          => $this->zone,
            'status'        => $this->status,
            'table_zone'    => new TableZoneResource($this->whenLoaded('tableZone')),
            'active_order'  => $this->whenLoaded('activeOrder', fn () => $this->activeOrder ? [
                'id'     => $this->activeOrder->id,
                'status' => $this->activeOrder->status,
                'items'  => $this->activeOrder->items_count ?? $this->activeOrder->items->count(),
                'total'  => $this->activeOrder->total,
            ] : null),
            'created_at'    => $this->created_at?->toIso8601String(),
            'updated_at'    => $this->updated_at?->toIso8601String(),
        ];
    }
}
