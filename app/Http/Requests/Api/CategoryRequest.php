<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $logoRule = $this->isMethod('post')
            ? 'required|image|mimes:png,jpg,jpeg,webp,svg|max:2048'
            : 'nullable|image|mimes:png,jpg,jpeg,webp,svg|max:2048';

        return [
            'name'   => 'required|string|max:255',
            'active' => 'nullable|boolean',
            'logo'   => $logoRule,
        ];
    }
}
