<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AboutRequest extends FormRequest
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
            'about_en' => 'nullable',
            'about_ar' => 'nullable',
            'mission_en' => 'nullable',
            'mission_ar' => 'nullable',
            'vision_en' => 'nullable',
            'vision_ar' => 'nullable',
            'goals_en' => 'nullable',
            'goals_ar' => 'nullable',
            'image' => 'nullable|mimes:jpg,jpeg,png,webp,gif,svg|max:2000',
        ];

    }




}
