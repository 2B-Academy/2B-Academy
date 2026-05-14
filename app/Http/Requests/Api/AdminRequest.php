<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class AdminRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('admin')?->id ?? '';

        return [
            'name'                  => 'required|string|max:255',
            'email'                 => 'required|email|unique:admins,email,' . $id,
            'password'              => ($this->isMethod('POST') ? 'required' : 'nullable') . '|string|min:8|confirmed',
            'password_confirmation' => 'nullable|string',
            'role'                  => 'required|string|exists:roles,name',
        ];
    }
}
