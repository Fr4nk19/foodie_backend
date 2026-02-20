<?php

namespace App\Http\Requests\Api\V1\User;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'       => ['required', 'string', 'max:255'],
            'email'      => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password'   => ['required', 'string', 'min:8'],
            'role'       => ['required', 'string', 'in:super_admin,company_admin,branch_manager,employee'],
            'company_id' => ['nullable', 'integer', 'exists:companies,id'],
            'branch_id'  => ['nullable', 'integer', 'exists:branches,id'],
            'is_active'  => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'      => 'El nombre es obligatorio.',
            'email.required'     => 'El correo electrónico es obligatorio.',
            'email.email'        => 'El correo electrónico no es válido.',
            'email.unique'       => 'Este correo electrónico ya está registrado.',
            'password.required'  => 'La contraseña es obligatoria.',
            'password.min'       => 'La contraseña debe tener al menos 8 caracteres.',
            'role.required'      => 'El rol es obligatorio.',
            'role.in'            => 'El rol seleccionado no es válido.',
            'company_id.exists'  => 'La empresa seleccionada no existe.',
            'branch_id.exists'   => 'La sucursal seleccionada no existe.',
        ];
    }
}
