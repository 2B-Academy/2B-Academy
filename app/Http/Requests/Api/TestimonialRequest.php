<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class TestimonialRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $imageRule = $this->isMethod('PUT')
            ? 'nullable|image|mimes:png,jpg,jpeg,webp|max:2000'
            : 'nullable|image|mimes:png,jpg,jpeg,webp|max:2000';

        return [
            'name'               => 'required|array',
            'name.ar'            => 'required|string|max:255',
            'name.en'            => 'nullable|string|max:255',
            'description'        => 'required|array',
            'description.ar'     => 'required|string',
            'description.en'     => 'nullable|string',
            'image'              => $imageRule,
            'active'             => 'nullable|boolean',
        ];
    }
}
