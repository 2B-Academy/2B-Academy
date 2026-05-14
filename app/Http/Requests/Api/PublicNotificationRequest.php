<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class PublicNotificationRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'title'        => 'required|array',
            'title.ar'     => 'required|string|max:255',
            'title.en'     => 'nullable|string|max:255',
            'body'         => 'required|array',
            'body.ar'      => 'required|string',
            'body.en'      => 'nullable|string',
            'for_public'   => 'nullable|boolean',
            'user_codes'   => 'nullable|array',
            'user_codes.*' => 'string',
        ];
    }
}
