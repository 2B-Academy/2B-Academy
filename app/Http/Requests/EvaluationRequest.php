<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EvaluationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'evaluation_category_id' => 'required|exists:evaluation_categories,id',
            'type' => 'required|in:text,five,ten',
            'title' => 'required',
            'is_required' => 'nullable|boolean',
        ];
    }

}
