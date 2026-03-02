<?php

namespace App\Http\Requests\Api\V1\Catalog;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCatMhMunicipioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $departamentoId = $this->input('cat_mh_departamento_id');

        return [
            'cat_mh_departamento_id' => ['required', 'integer', 'exists:cat_mh_departamento,id'],
            'codigo'                 => [
                'required',
                'string',
                'max:20',
                Rule::unique('cat_mh_municipio', 'codigo')
                    ->where('cat_mh_departamento_id', $departamentoId),
            ],
            'descripcion'            => ['required', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'cat_mh_departamento_id.required' => 'El departamento es obligatorio.',
            'cat_mh_departamento_id.exists'   => 'El departamento seleccionado no existe.',
            'codigo.required'                 => 'El código es obligatorio.',
            'codigo.unique'                   => 'El código ya existe para este departamento.',
            'descripcion.required'            => 'La descripción es obligatoria.',
        ];
    }
}
