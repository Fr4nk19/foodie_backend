<?php

namespace App\Http\Requests\Api\V1\Catalog;

use Illuminate\Foundation\Http\FormRequest;

class StoreCatMhMunicipioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'codigo'      => ['required', 'string', 'max:20', 'unique:cat_mh_municipio,codigo'],
            'descripcion' => ['required', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'codigo.required'      => 'El código es obligatorio.',
            'codigo.unique'        => 'El código ya existe en el catálogo.',
            'descripcion.required' => 'La descripción es obligatoria.',
        ];
    }
}
