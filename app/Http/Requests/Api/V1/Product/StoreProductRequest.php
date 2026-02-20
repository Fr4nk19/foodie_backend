<?php

namespace App\Http\Requests\Api\V1\Product;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $companyId = $this->route('company')?->id;

        return [
            'cat_mh_unidad_de_medida_id' => ['required', 'exists:cat_mh_unidades_de_medida,id'],
            'codigo'                     => ['nullable', 'string', 'max:50', "unique:products,codigo,NULL,id,company_id,{$companyId}"],
            'nombre'                     => ['required', 'string', 'max:255'],
            'descripcion'                => ['nullable', 'string'],
            'precio'                     => ['required', 'numeric', 'min:0'],
            'peso'                       => ['nullable', 'numeric', 'min:0'],
            'tamanio'                    => ['nullable', 'string', 'max:100'],
            'imagen'                     => ['nullable', 'string', 'max:500'],
            'status'                     => ['nullable', 'in:active,inactive'],
            'atributos'                  => ['nullable', 'array'],
        ];
    }

    public function messages(): array
    {
        return [
            'cat_mh_unidad_de_medida_id.required' => 'La unidad de medida es obligatoria.',
            'cat_mh_unidad_de_medida_id.exists'   => 'La unidad de medida seleccionada no existe.',
            'codigo.unique'                        => 'El código SKU ya existe para esta empresa.',
            'nombre.required'                      => 'El nombre del producto es obligatorio.',
            'precio.required'                      => 'El precio es obligatorio.',
            'precio.min'                           => 'El precio no puede ser negativo.',
        ];
    }
}
