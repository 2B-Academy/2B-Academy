<?php

namespace App\Http\Controllers\apis;

use App\Http\Requests\Api\AdminRequest;
use App\Http\Resources\AdminResource;
use App\Models\Admin;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        
        $admins = Admin::with('roles')
            ->when($request->get('search'), fn ($q, $s) => $q->where('name', 'like', "%$s%")
                ->orWhere('email', 'like', "%$s%"))
            ->orderByDesc('id')
            ->paginate((int) $request->get('per_page', 20));

        return $this->paginated(__('messages.retrieved'), $admins);
    }

    public function show(Admin $admin): JsonResponse
    {
    
        $admin->load('roles');
        return $this->success(__('messages.retrieved'), new AdminResource($admin));
    }

    public function store(AdminRequest $request): JsonResponse
    {
        $data             = $request->validated();
        $data['password'] = Hash::make($data['password']);
        $role             = $data['role'];
        unset($data['role'], $data['password_confirmation']);

        $admin = Admin::create($data);
        $admin->assignRole($role);
        $admin->load('roles');

        return $this->created(__('messages.created'), new AdminResource($admin));
    }

    public function update(Admin $admin, AdminRequest $request): JsonResponse
    {
        $data = $request->validated();
        $role = $data['role'];
        unset($data['role'], $data['password_confirmation']);

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $admin->update($data);
        $admin->syncRoles([$role]);
        $admin->load('roles');

        return $this->success(__('messages.updated'), new AdminResource($admin));
    }

    public function destroy(Admin $admin): JsonResponse
    {
        $admin->delete();
        return $this->deleted(__('messages.deleted'));
    }
}
