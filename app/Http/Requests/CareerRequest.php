<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CareerRequest extends FormRequest
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
            'title_en' => 'required|max:255',
            'title_ar' => 'nullable|max:255',
            'description_en' => 'required',
            'description_ar' => 'nullable',
            'active' => 'nullable|boolean',
        ];
    }

}
