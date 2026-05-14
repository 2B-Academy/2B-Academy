<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ArticleRequest extends FormRequest
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
        $required_image = request()->isMethod('put') ?
            'nullable|mimes:png,jpg,jpeg,webp,svg,gif|max:2000' :
            'required|mimes:png,jpg,jpeg,webp,svg,gif|max:2000';
        return [
            'type' => 'nullable',
            'title_en' => 'nullable|max:255',
            'title_ar' => 'required|max:255',
            'description_en' => 'nullable',
            'description_ar' => 'required',
            'slug' => 'required|max:255',
            'date_publish' => 'nullable|date',
            'image' => $required_image,
            'is_home' => 'nullable|boolean',
            'active' => 'nullable|boolean',
        ];
    }

}
