<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RoleRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    /**
     * The auth guard a new role belongs to. Defaults to `admin` because this
     * `RoleRequest` is only used by the admin-panel role-management UI, where
     * every permission in the system lives under the `admin` guard. Existing
     * roles keep their own guard via the route-bound model.
     */
    public function guardName(): string
    {
        return $this->route('role')?->guard_name
            ?? (string) ($this->input('guard_name') ?: 'admin');
    }

    public function rules(): array
    {
        $guard = $this->guardName();

        return [
            'name' => [
                'required', 'string', 'max:255',
                // Spatie's roles table is unique on `(name, guard_name)` —
                // scope our rule the same way so an admin can have a role
                // named "instructor" under the admin guard even if a separate
                // web-guard "instructor" already exists.
                Rule::unique('roles', 'name')
                    ->where(fn ($q) => $q->where('guard_name', $guard))
                    ->ignore($this->route('role')?->id),
            ],
            'guard_name'    => 'nullable|string|in:admin,web',
            'permissions'   => 'nullable|array',
            'permissions.*' => 'string|exists:permissions,name',
        ];
    }
}
