<?php

namespace App\Http\Requests\Api\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validation for the "Edit User" modal from the 2026 Figma redesign.
 *
 * Every field is optional ("sometimes") so the admin can patch individual
 * attributes without re-supplying the entire form.  The email uniqueness
 * check is scoped to the source table indicated by the URL.
 */
class AdminUserUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $source = (string) $this->route('source');
        $id     = (int)    $this->route('id');

        $table = match ($source) {
            'admin'      => 'admins',
            'instructor' => 'instructors',
            default      => 'users',
        };

        return [
            'name_en'         => ['sometimes', 'nullable', 'string', 'max:255'],
            'name_ar'         => ['sometimes', 'nullable', 'string', 'max:255'],
            'email'           => [
                'sometimes', 'nullable', 'email', 'max:255',
                Rule::unique($table, 'email')->ignore($id),
            ],
            'role'            => [
                'sometimes', 'nullable',
                Rule::exists('roles', 'name')->where(fn ($q) => $q->where('guard_name', 'admin')),
            ],
            'department_name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'phone'           => ['sometimes', 'nullable', 'string', 'max:50'],
            'learner_type'    => ['sometimes', 'nullable', Rule::in(['online', 'offline', 'hybrid'])],
            'status'          => ['sometimes', 'nullable', Rule::in(['active', 'inactive', 'deactivated'])],
            'image'           => ['sometimes', 'nullable', 'image', 'mimes:png,jpg,jpeg,webp,svg,gif', 'max:3072'],
        ];
    }
}
