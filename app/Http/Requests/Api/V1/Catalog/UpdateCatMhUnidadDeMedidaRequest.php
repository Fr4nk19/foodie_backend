<?php

namespace App\Http\Requests\Api\V1\Catalog;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCatMhUnidadDeMedidaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'codigo'      => [
                'sometimes', 'required', 'string', 'max:10',
                Rule::unique('cat_mh_unidades_de_medida', 'codigo')
                    ->ignore($this->route('unidad_de_medida')),
            ],
            'descripcion' => ['sometimes', 'required', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'codigo.unique'        => 'El código ya existe en el catálogo.',
            'descripcion.required' => 'La descripción es obligatoria.',
        ];
    }
}
