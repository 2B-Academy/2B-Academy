<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class SyncEnrollmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'for_public' => 'sometimes|boolean',
            'user_ids'   => 'sometimes|array',
            'user_ids.*' => 'integer|exists:users,id',
        ];
    }
}
