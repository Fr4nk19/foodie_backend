<?php

namespace App\Http\Requests\Api\V1\Catalog;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCatMhTipoEstablecimientoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('tipo_establecimiento')?->id;

        return [
            'codigo'      => ['sometimes', 'required', 'string', 'max:20',
                              Rule::unique('cat_mh_tipo_establecimiento', 'codigo')->ignore($id)],
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
