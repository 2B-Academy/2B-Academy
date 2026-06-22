<?php

namespace App\Http\Controllers\apis\Admin;

use App\Http\Controllers\apis\ApiController;
use App\Http\Requests\Api\Admin\AdminRoleStoreRequest;
use App\Http\Requests\Api\Admin\AdminRoleUpdateRequest;
use App\Services\Admin\AdminRoleService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Admin endpoints powering the 2026 Roles redesign.
 *
 * Strictly additive — the legacy public endpoints
 * (App\Http\Controllers\apis\RoleController @ /api/v1/roles) and
 * /api/v1/permissions are left untouched.
 *
 * Routes:
 *   GET    /api/v1/admin/roles
 *   GET    /api/v1/admin/roles/sections
 *   POST   /api/v1/admin/roles
 *   GET    /api/v1/admin/roles/{id}
 *   PUT    /api/v1/admin/roles/{id}
 *   DELETE /api/v1/admin/roles/{id}
 */
class AdminRoleController extends ApiController
{
    public function __construct(private readonly AdminRoleService $service) {}

    /** GET /api/v1/admin/roles */
    public function index(Request $request): JsonResponse
    {
        $payload = $this->service->list(
            $request->string('search')->toString() ?: null,
        );

        return $this->success(__('messages.retrieved'), $payload);
    }

    /** GET /api/v1/admin/roles/sections */
    public function sections(): JsonResponse
    {
        return $this->success(__('messages.retrieved'), $this->service->sectionCatalog());
    }

    /** GET /api/v1/admin/roles/{id} */
    public function show(int $id): JsonResponse
    {
        try {
            return $this->success(__('messages.retrieved'), $this->service->show($id));
        } catch (ModelNotFoundException) {
            return $this->notFound();
        }
    }

    /** POST /api/v1/admin/roles */
    public function store(AdminRoleStoreRequest $request): JsonResponse
    {
        $row = $this->service->create($request->validated());
        return $this->created(__('messages.created'), $row);
    }

    /** PUT /api/v1/admin/roles/{id} */
    public function update(AdminRoleUpdateRequest $request, int $id): JsonResponse
    {
        try {
            $row = $this->service->update($id, $request->validated());
            return $this->success(__('messages.updated'), $row);
        } catch (ModelNotFoundException) {
            return $this->notFound();
        }
    }

    /** DELETE /api/v1/admin/roles/{id} */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->service->delete($id);
            return $this->success(__('messages.deleted'), null);
        } catch (ModelNotFoundException) {
            return $this->notFound();
        }
    }
}
