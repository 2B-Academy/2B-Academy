<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SeoRequest extends FormRequest
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
            'meta_title_en' => 'required|max:255',
            'meta_title_ar' => 'nullable|max:255',
            'meta_description_en' => 'required',
            'meta_description_ar' => 'nullable',
            'meta_keywords_en' => 'required',
            'meta_keywords_ar' => 'nullable',
            'author_en' => 'nullable|max:255',
            'author_ar' => 'nullable|max:255',
            'site_name_en' => 'nullable|max:255',
            'site_name_ar' => 'nullable|max:255',
            'canonical' => 'nullable|url|max:255',
        ];

    }




}
