<?php

namespace App\Http\Requests\Api\V1\Company;

use Illuminate\Foundation\Http\FormRequest;

class StoreCompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:companies,email'],
            'phone'    => ['nullable', 'string', 'max:30'],
            'address'  => ['nullable', 'string', 'max:500'],
            'city'     => ['nullable', 'string', 'max:100'],
            'state'    => ['nullable', 'string', 'max:100'],
            'country'  => ['nullable', 'string', 'max:100'],
            'timezone' => ['nullable', 'string', 'timezone:all'],
            'plan'     => ['nullable', 'string', 'in:free,basic,premium'],

            // Primera sucursal (opcional, se crea automáticamente si no se provee)
            'branch_name'    => ['nullable', 'string', 'max:255'],
            'branch_address' => ['nullable', 'string', 'max:500'],
        ];
    }
}
