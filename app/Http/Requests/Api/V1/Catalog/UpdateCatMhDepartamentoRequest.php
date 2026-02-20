<?php

namespace App\Http\Requests\Api\V1\Catalog;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCatMhDepartamentoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('departamento')?->id;

        return [
            'codigo'      => ['sometimes', 'required', 'string', 'max:20',
                              Rule::unique('cat_mh_departamento', 'codigo')->ignore($id)],
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
