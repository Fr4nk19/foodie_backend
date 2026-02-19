<?php

namespace App\Http\Requests\Api\V1\Company;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCompanyEconomicActivityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'is_primary' => ['required', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'is_primary.required' => 'El campo principal es obligatorio.',
            'is_primary.boolean'  => 'El campo principal debe ser verdadero o falso.',
        ];
    }
}
