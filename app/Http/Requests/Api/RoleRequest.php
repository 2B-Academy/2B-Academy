<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class RoleRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'         => 'required|string|max:255|unique:roles,name,' . ($this->role?->id ?? 'NULL'),
            'permissions'  => 'nullable|array',
            'permissions.*'=> 'string|exists:permissions,name',
        ];
    }
}
