<?php

namespace App\Http\Requests\Api\V1\Order;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'table_id'  => ['sometimes', 'nullable', 'integer', 'exists:tables,id'],
            'waiter_id' => ['sometimes', 'nullable', 'integer', 'exists:users,id'],
            'type'      => ['sometimes', 'string', 'in:dine_in,takeout,delivery'],
            'priority'  => ['sometimes', 'string', 'in:normal,high,urgent'],
            'notes'     => ['sometimes', 'nullable', 'string', 'max:1000'],
        ];
    }
}
