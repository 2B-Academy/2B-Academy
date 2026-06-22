<?php

namespace App\Http\Requests\Api\Admin;

use App\Services\Admin\AdminRoleService;
use Illuminate\Foundation\Http\FormRequest;

class AdminRoleUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name_en'        => ['sometimes', 'string', 'max:191'],
            'name_ar'        => ['sometimes', 'nullable', 'string', 'max:191'],
            'description_en' => ['sometimes', 'nullable', 'string', 'max:500'],
            'description_ar' => ['sometimes', 'nullable', 'string', 'max:500'],
            'color'          => ['sometimes', 'nullable', 'string', 'in:' . implode(',', AdminRoleService::COLORS)],
            'view_keys'      => ['sometimes', 'nullable', 'array'],
            'view_keys.*'    => ['string'],
        ];
    }
}
