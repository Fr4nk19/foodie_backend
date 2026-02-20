<?php

namespace App\Http\Requests\Api\V1\Company;

use Illuminate\Foundation\Http\FormRequest;

class StoreCompanyEconomicActivityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $companyId = $this->route('company')?->id;

        return [
            'cat_mhactividad_id' => [
                'required',
                'integer',
                'exists:cat_mh_actividades_economicas,id',
                // No duplicate for same company (ignoring soft-deleted)
                function ($attribute, $value, $fail) use ($companyId) {
                    $exists = \App\Models\EconomicActivityByCompany::where('company_id', $companyId)
                        ->where('cat_mhactividad_id', $value)
                        ->exists();
                    if ($exists) {
                        $fail('Esta actividad económica ya está asignada a la empresa.');
                    }
                },
            ],
            'is_primary' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'cat_mhactividad_id.required' => 'Debe seleccionar una actividad económica.',
            'cat_mhactividad_id.exists'   => 'La actividad económica seleccionada no existe.',
        ];
    }
}
