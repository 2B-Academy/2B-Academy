<?php

namespace App\Http\Controllers\apis;

use App\Http\Requests\Api\RoleRequest;
use App\Http\Resources\RoleResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RoleController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $roles = Role::with('permissions')
            ->when($request->get('search'), fn ($q, $s) => $q->where('name', 'like', "%$s%"))
            ->orderByDesc('id')
            ->paginate((int) $request->get('per_page', 20));

        return $this->paginated(__('messages.retrieved'), $roles);
    }

    public function all(): JsonResponse
    {
        return $this->success(__('messages.retrieved'),
            RoleResource::collection(Role::orderBy('name')->get())
        );
    }

    public function show(Role $role): JsonResponse
    {
        $role->load('permissions');
        return $this->success(__('messages.retrieved'), new RoleResource($role));
    }

    public function store(RoleRequest $request): JsonResponse
    {
        $role = Role::create(['name' => $request->validated()['name']]);
        if (!empty($request->validated()['permissions'])) {
            $role->syncPermissions($request->validated()['permissions']);
        }
        $role->load('permissions');
        return $this->created(__('messages.created'), new RoleResource($role));
    }

    public function update(Role $role, RoleRequest $request): JsonResponse
    {
        $role->update(['name' => $request->validated()['name']]);
        $role->syncPermissions($request->validated()['permissions'] ?? []);
        $role->load('permissions');
        return $this->success(__('messages.updated'), new RoleResource($role));
    }

    public function destroy(Role $role): JsonResponse
    {
        $role->delete();
        return $this->deleted(__('messages.deleted'));
    }
}
