<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PublicNotificationRequest extends FormRequest
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
            'title' => 'required|max:255',
            'body' => 'required',
            'for_public' => 'nullable|boolean',
            'users' => 'nullable|array',
            'users.*' => 'exists:users,machine_code',
            'users_sheet' => 'nullable|mimes:xlsx|max:10000',
        ];
    }

     public function attributes()
    {
           return [
               'title.required',
               'title.max',
               'description.required',
               'users_sheet,mimes',
               'users_sheet.max'
           ];
    }

}
