<?php

namespace App\Http\Requests\Api\V1\Table;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTableRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $branchId = $this->route('branch')?->id;
        $tableId  = $this->route('table')?->id;

        return [
            'number'   => ['sometimes', 'required', 'integer', 'min:1', "unique:tables,number,{$tableId},id,branch_id,{$branchId},deleted_at,NULL"],
            'capacity' => ['sometimes', 'required', 'integer', 'min:1', 'max:50'],
            'zone'     => ['sometimes', 'required', 'string', 'max:100'],
            'status'   => ['sometimes', 'string', 'in:available,occupied,reserved,cleaning'],
        ];
    }

    public function messages(): array
    {
        return [
            'number.unique'   => 'Ya existe una mesa con ese número en esta sucursal.',
            'capacity.min'    => 'La capacidad debe ser al menos 1.',
        ];
    }
}
