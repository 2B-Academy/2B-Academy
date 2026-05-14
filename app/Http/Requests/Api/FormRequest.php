<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class FormRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'title'      => 'required|array',
            'title.ar'   => 'required|string|max:255',
            'title.en'   => 'nullable|string|max:255',
            'duration'   => 'required|integer|min:1',
            'full_mark'  => 'required|integer|min:1',
            'active'     => 'nullable|boolean',
        ];
    }
}
