<?php

namespace App\Http\Requests\Api\V1\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('user')?->id;

        return [
            'name'       => ['sometimes', 'required', 'string', 'max:255'],
            'email'      => [
                'sometimes',
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'password'   => ['sometimes', 'nullable', 'string', 'min:8'],
            'role'       => ['sometimes', 'required', 'string', 'in:super_admin,company_admin,branch_manager,employee'],
            'company_id' => ['sometimes', 'nullable', 'integer', 'exists:companies,id'],
            'branch_id'  => ['sometimes', 'nullable', 'integer', 'exists:branches,id'],
            'is_active'  => ['sometimes', 'boolean'],
            'job_type'   => ['sometimes', 'nullable', 'string', 'in:kitchen,waiter'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique'      => 'Este correo electrónico ya está en uso.',
            'email.email'       => 'El correo electrónico no es válido.',
            'password.min'      => 'La contraseña debe tener al menos 8 caracteres.',
            'role.in'           => 'El rol seleccionado no es válido.',
            'company_id.exists' => 'La empresa seleccionada no existe.',
            'branch_id.exists'  => 'La sucursal seleccionada no existe.',
        ];
    }
}
