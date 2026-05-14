<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CourseLectureRequest extends FormRequest
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
        $rules = [
            'section_id' => 'required|exists:course_sections,id',
            'title' => 'required|max:255',
            'type' => 'required|in:url,upload',
            'video' => 'required',
        ];
        if ($this->input('type') === 'url') {
            $rules['video'] .= '|url';
        }
        return $rules;
    }

     public function attributes()
    {
           return [
               'section_id.required',
               'section_id.exists',
               'title.required',
               'title.max',
               'type.required',
               'type.in',
               'video.required',
           ];
    }

}
