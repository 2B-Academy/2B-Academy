<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAdminProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'  => 'sometimes|string|max:255',
            'email' => 'sometimes|email|max:255|unique:admins,email,' . $this->user()?->id,
        ];
    }
}
