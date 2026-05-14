<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
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
        $email =  request()->isMethod('put') ?
            'required|email|max:255|unique:users,email,'.auth()->user()->id.',id' :
            'required|unique:users,email|email|max:255';
        return [
            'name' => 'required|max:255',
            'email' => $email,
            'password' => 'nullable',
        ];

    }



}
