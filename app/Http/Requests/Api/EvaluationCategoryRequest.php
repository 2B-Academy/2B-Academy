<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class EvaluationCategoryRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'    => 'required|array',
            'name.ar' => 'required|string|max:255',
            'name.en' => 'nullable|string|max:255',
        ];
    }
}
