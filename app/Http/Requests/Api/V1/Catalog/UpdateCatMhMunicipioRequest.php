<?php

namespace App\Http\Requests\Api\V1\Catalog;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCatMhMunicipioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $municipio      = $this->route('municipio');
        $id             = $municipio?->id;
        $departamentoId = $this->input('cat_mh_departamento_id', $municipio?->cat_mh_departamento_id);

        return [
            'cat_mh_departamento_id' => ['sometimes', 'required', 'integer', 'exists:cat_mh_departamento,id'],
            'codigo'                 => [
                'sometimes',
                'required',
                'string',
                'max:20',
                Rule::unique('cat_mh_municipio', 'codigo')
                    ->where('cat_mh_departamento_id', $departamentoId)
                    ->ignore($id),
            ],
            'descripcion'            => ['sometimes', 'required', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'cat_mh_departamento_id.exists'   => 'El departamento seleccionado no existe.',
            'codigo.unique'                   => 'El código ya existe para este departamento.',
            'descripcion.required'            => 'La descripción es obligatoria.',
        ];
    }
}
