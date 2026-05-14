<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CourseRatingRequest extends FormRequest
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
            'rating' => 'required|between:1,5',
            'comment' => 'required',
        ];
    }

    public function attributes()
    {
        return [
            'rating.required',
            'rating.between',
            'comment.required',
        ];
    }

}
