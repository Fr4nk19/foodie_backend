<?php

namespace App\Http\Requests\Api\V1\Inventory;

use Illuminate\Foundation\Http\FormRequest;

class StoreInventoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $branchId = $this->route('branch')?->id;

        return [
            'product_id'      => ['required', 'exists:products,id', "unique:inventory,product_id,NULL,id,branch_id,{$branchId}"],
            'cantidad'        => ['required', 'numeric', 'min:0'],
            'cantidad_minima' => ['nullable', 'numeric', 'min:0'],
            'cantidad_maxima' => ['nullable', 'numeric', 'min:0'],
            'costo_unitario'  => ['nullable', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.required' => 'El producto es obligatorio.',
            'product_id.exists'   => 'El producto seleccionado no existe.',
            'product_id.unique'   => 'Este producto ya está registrado en el inventario de esta sucursal.',
            'cantidad.required'   => 'La cantidad es obligatoria.',
            'cantidad.min'        => 'La cantidad no puede ser negativa.',
        ];
    }
}
