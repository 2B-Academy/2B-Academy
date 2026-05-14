<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InstructorRequest extends FormRequest
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
            'name' => 'required|max:255',
            'bio' => 'required',
            'email' => 'required|email|unique:instructors,email',
            'job_title' => 'required|max:255',
            'image' => $required_image,
        ];
    }

     public function attributes()
    {
           return [
               'name.required',
               'name.max',
               'bio.required',
               'email.required',
               'email.email',
               'email.unique',
               'job_title.required',
               'job_title.max',
               'image.required',
               'image.mimes',
               'image.max',
           ];
    }

}
