<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class CourseAssignmentRequest extends FormRequest
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
            'file.*' => 'required|mimes:pdf,xls,xlsx,csv,doc,docx|max:10000',
        ];
    }

     public function attributes()
    {
           return [
               'title.*.required',
               'title.*.max',
               'file.*.required',
               'file.*.mimes',
               'file.*.max',
           ];
    }

}
