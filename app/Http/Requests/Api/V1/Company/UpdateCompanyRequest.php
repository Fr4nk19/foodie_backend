<?php

namespace App\Http\Requests\Api\V1\Company;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $companyId = $this->route('company');

        return [
            'name'     => ['sometimes', 'required', 'string', 'max:255'],
            'email'    => ['sometimes', 'required', 'string', 'email', 'max:255',
                            Rule::unique('companies', 'email')->ignore($companyId)],
            'phone'    => ['nullable', 'string', 'max:30'],
            'address'  => ['nullable', 'string', 'max:500'],
            'city'     => ['nullable', 'string', 'max:100'],
            'state'    => ['nullable', 'string', 'max:100'],
            'country'  => ['nullable', 'string', 'max:100'],
            'timezone' => ['nullable', 'string', 'timezone:all'],
            'plan'     => ['nullable', 'string', 'in:free,basic,premium'],
            'status'   => ['nullable', 'string', 'in:active,inactive,suspended'],
        ];
    }
}
