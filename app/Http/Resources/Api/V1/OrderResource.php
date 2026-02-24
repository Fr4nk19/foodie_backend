<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'branch_id'     => $this->branch_id,
            'table_id'      => $this->table_id,
            'waiter_id'     => $this->waiter_id,
            'type'          => $this->type,
            'status'        => $this->status,
            'priority'       => $this->priority,
            'total'         => $this->total,
            'notes'         => $this->notes,
            'cancel_reason' => $this->cancel_reason,
            'table'         => $this->whenLoaded('table', fn () => $this->table ? [
                'id'     => $this->table->id,
                'number' => $this->table->number,
                'zone'   => $this->table->zone,
            ] : null),
            'waiter'        => $this->whenLoaded('waiter', fn () => $this->waiter ? [
                'id'   => $this->waiter->id,
                'name' => $this->waiter->name,
            ] : null),
            'items'         => OrderItemResource::collection($this->whenLoaded('items')),
            'items_count'   => $this->when(isset($this->items_count), $this->items_count),
            'created_at'    => $this->created_at?->toIso8601String(),
            'updated_at'    => $this->updated_at?->toIso8601String(),
        ];
    }
}
