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
            // Password set directly from the Add User modal (replaces the
            // legacy Controllers screen). `confirmed` pairs it with the
            // `password_confirmation` field sent by the dialog.
            'password'              => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['required', 'string'],
            'department_name' => ['nullable', 'string', 'max:255'],
            'phone'           => ['nullable', 'string', 'max:50'],
            // "Brief on the instructor" — bilingual, only meaningful for the
            // instructor role (persisted to the instructor's bio).
            'brief_en'        => ['nullable', 'string', 'max:2000'],
            'brief_ar'        => ['nullable', 'string', 'max:2000'],
            'learner_type'    => ['nullable', Rule::in(['online', 'offline', 'hybrid'])],
            'image'           => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp,svg,gif', 'max:3072'],
        ];
    }
}
