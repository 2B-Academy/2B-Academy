<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class CourseResourceRequest extends FormRequest
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
            'title.*' => 'required|max:255',
            'link.*' => 'nullable|url',
            'file.*' => 'nullable|mimes:pdf,doc,docx,xls,xlsx|max:10000',
        ];
    }

     public function attributes()
    {
           return [
               'title.*.required',
               'title.*.max',
               'link.*.url',
               'file.*.mimes',
               'file.*.max',
           ];
    }

}
