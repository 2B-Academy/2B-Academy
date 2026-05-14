<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
            'name' => 'required|max:255',
            'phone' => 'nullable|max:255',
            'department_name' => 'required|max:255',
        ];
    }

     public function attributes()
    {
           return [
               'name.required',
               'name.max',
               'phone.max',
               'department_name.required',
               'department_name.max',
           ];
    }

}
