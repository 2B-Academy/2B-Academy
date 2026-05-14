<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class AssignmentReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'feedback' => 'nullable|string|max:2000',
            'score'    => 'nullable|string|max:50',
        ];
    }
}
