<?php

namespace App\Http\Requests\Api\V1\Table;

use Illuminate\Foundation\Http\FormRequest;

class StoreTableRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $branchId = $this->route('branch')?->id;

        return [
            'number'   => ['required', 'integer', 'min:1', "unique:tables,number,NULL,id,branch_id,{$branchId},deleted_at,NULL"],
            'capacity' => ['required', 'integer', 'min:1', 'max:50'],
            'zone'     => ['required', 'string', 'max:100'],
            'status'   => ['sometimes', 'string', 'in:available,occupied,reserved,cleaning'],
        ];
    }

    public function messages(): array
    {
        return [
            'number.required' => 'El número de mesa es obligatorio.',
            'number.unique'   => 'Ya existe una mesa con ese número en esta sucursal.',
            'capacity.required' => 'La capacidad es obligatoria.',
            'capacity.min'    => 'La capacidad debe ser al menos 1.',
            'zone.required'   => 'La zona es obligatoria.',
        ];
    }
}
