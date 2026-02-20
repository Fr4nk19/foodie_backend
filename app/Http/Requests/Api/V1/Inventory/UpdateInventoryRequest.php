<?php

namespace App\Http\Requests\Api\V1\Inventory;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInventoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cantidad'        => ['sometimes', 'required', 'numeric', 'min:0'],
            'cantidad_minima' => ['nullable', 'numeric', 'min:0'],
            'cantidad_maxima' => ['nullable', 'numeric', 'min:0'],
            'costo_unitario'  => ['nullable', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'cantidad.min' => 'La cantidad no puede ser negativa.',
        ];
    }
}
