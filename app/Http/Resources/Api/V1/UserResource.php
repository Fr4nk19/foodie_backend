<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'email'      => $this->email,
            'role'       => $this->role,
            'job_type'   => $this->job_type,
            'is_active'  => $this->is_active,
            'company_id' => $this->company_id,
            'branch_id'  => $this->branch_id,
            'company'    => new CompanyResource($this->whenLoaded('company')),
            'branch'     => new BranchResource($this->whenLoaded('branch')),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
