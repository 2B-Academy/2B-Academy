<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class ContactRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|max:255',
            'phone'        => 'nullable|string|max:50',
            'job_title'    => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'guests'       => 'nullable|array|max:20',
            'guests.*'     => 'email|max:255',
        ];
    }
}
