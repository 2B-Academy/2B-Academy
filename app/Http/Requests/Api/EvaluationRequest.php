<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class EvaluationRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'evaluation_category_id' => 'required|integer|exists:evaluation_categories,id',
            'type'                   => 'required|in:text,five,ten',
            'title'                  => 'required|array',
            'title.ar'               => 'required|string',
            'title.en'               => 'nullable|string',
            'is_required'            => 'nullable|boolean',
        ];
    }
}
