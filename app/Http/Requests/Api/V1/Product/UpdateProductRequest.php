<?php

namespace App\Http\Requests\Api\V1\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $companyId = $this->route('company')?->id;
        $productId = $this->route('product')?->id;

        return [
            'product_category_id'        => [
                'nullable',
                Rule::exists('product_categories', 'id')->where('company_id', $companyId),
            ],
            'cat_mh_unidad_de_medida_id' => ['sometimes', 'required', 'exists:cat_mh_unidades_de_medida,id'],
            'codigo'                     => [
                'nullable', 'string', 'max:50',
                Rule::unique('products', 'codigo')
                    ->where('company_id', $companyId)
                    ->ignore($productId),
            ],
            'nombre'                     => ['sometimes', 'required', 'string', 'max:255'],
            'descripcion'                => ['nullable', 'string'],
            'precio'                     => ['sometimes', 'required', 'numeric', 'min:0'],
            'peso'                       => ['nullable', 'numeric', 'min:0'],
            'tamanio'                    => ['nullable', 'string', 'max:100'],
            'imagen'                     => ['nullable', 'string', 'max:500'],
            'status'                     => ['nullable', 'in:active,inactive'],
            'track_stock'                => ['nullable', 'boolean'],
            'atributos'                  => ['nullable', 'array'],
        ];
    }

    public function messages(): array
    {
        return [
            'product_category_id.exists'        => 'La categoría seleccionada no existe o no pertenece a esta empresa.',
            'cat_mh_unidad_de_medida_id.exists' => 'La unidad de medida seleccionada no existe.',
            'codigo.unique'                      => 'El código SKU ya existe para esta empresa.',
            'nombre.required'                    => 'El nombre del producto es obligatorio.',
            'precio.min'                         => 'El precio no puede ser negativo.',
        ];
    }
}
