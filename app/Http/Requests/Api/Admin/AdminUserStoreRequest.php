<?php

namespace App\Http\Requests\Api\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validation for the "Add New User" modal from the 2026 Figma redesign
 * (Figma 529:38878).
 *
 * Captures bilingual full names, contact email, the chosen Role
 * (sourced dynamically from the `roles` table — see AdminUserService::
 * filterOptions()), and an optional avatar image. The legacy free-text
 * "Job Role" field is gone in this revision.
 */
class AdminUserStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $role = (string) $this->input('role');
        $table = match ($role) {
            'admin', 'superAdmin' => 'admins',
            'instructor'          => 'instructors',
            default               => 'users',
        };

        return [
            'name_en'         => ['required', 'string', 'max:255'],
            'name_ar'         => ['required', 'string', 'max:255'],
            'email'           => ['required', 'email', 'max:255', Rule::unique($table, 'email')],
            'role'            => [
                'required',
                Rule::exists('roles', 'name')->where(fn ($q) => $q->where('guard_name', 'admin')),
            ],
            'department_name' => ['nullable', 'string', 'max:255'],
            'phone'           => ['nullable', 'string', 'max:50'],
            'learner_type'    => ['nullable', Rule::in(['online', 'offline', 'hybrid'])],
            'image'           => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp,svg,gif', 'max:3072'],
        ];
    }
}
