<?php

namespace App\Http\Requests\Api\Admin;

use App\Services\Admin\AdminRoleService;
use Illuminate\Foundation\Http\FormRequest;

class AdminRoleStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // role middleware on the route already gates this.
    }

    public function rules(): array
    {
        return [
            'name_en'        => ['required', 'string', 'max:191'],
            'name_ar'        => ['required', 'string', 'max:191'],
            'description_en' => ['nullable', 'string', 'max:500'],
            'description_ar' => ['nullable', 'string', 'max:500'],
            'color'          => ['nullable', 'string', 'in:' . implode(',', AdminRoleService::COLORS)],
            'view_keys'      => ['nullable', 'array'],
            'view_keys.*'    => ['string'],
        ];
    }
}
