<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class InstructorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $imageRule = $this->isMethod('post')
            ? 'required|image|mimes:png,jpg,jpeg,webp|max:2048'
            : 'nullable|image|mimes:png,jpg,jpeg,webp|max:2048';

        return [
            'name'  => 'required|string|max:255',
            'bio'   => 'nullable|string',
            'image' => $imageRule,
        ];
    }
}
