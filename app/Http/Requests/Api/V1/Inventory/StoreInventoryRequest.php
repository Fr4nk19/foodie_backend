<?php

namespace App\Http\Requests\Api\V1\Inventory;

use App\Models\Product;
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

        // Si el producto no lleva control de stock, la cantidad es opcional.
        $product    = Product::find($this->input('product_id'));
        $trackStock = $product ? $product->track_stock : true;

        $cantidadRules = $trackStock
            ? ['required', 'numeric', 'min:0']
            : ['nullable', 'numeric', 'min:0'];

        return [
            'product_id'      => ['required', 'exists:products,id', "unique:inventory,product_id,NULL,id,branch_id,{$branchId}"],
            'cantidad'        => $cantidadRules,
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
            'cantidad.required'   => 'La cantidad es obligatoria para productos con control de stock.',
            'cantidad.min'        => 'La cantidad no puede ser negativa.',
        ];
    }
}
