<?php

namespace App\Http\Requests\Api\V1\Order;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'table_id'            => ['nullable', 'integer', 'exists:tables,id'],
            'waiter_id'           => ['nullable', 'integer', 'exists:users,id'],
            'type'                => ['sometimes', 'string', 'in:dine_in,takeout,delivery'],
            'priority'            => ['sometimes', 'string', 'in:normal,high,urgent'],
            'notes'               => ['nullable', 'string', 'max:1000'],
            'items'               => ['required', 'array', 'min:1'],
            'items.*.product_id'  => ['required', 'integer', 'exists:products,id'],
            'items.*.quantity'    => ['required', 'integer', 'min:1'],
            'items.*.notes'       => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'items.required'             => 'El pedido debe tener al menos un producto.',
            'items.min'                  => 'El pedido debe tener al menos un producto.',
            'items.*.product_id.required' => 'Cada ítem debe tener un producto.',
            'items.*.product_id.exists'  => 'Uno de los productos seleccionados no existe.',
            'items.*.quantity.required'  => 'La cantidad es obligatoria.',
            'items.*.quantity.min'       => 'La cantidad debe ser al menos 1.',
        ];
    }
}
