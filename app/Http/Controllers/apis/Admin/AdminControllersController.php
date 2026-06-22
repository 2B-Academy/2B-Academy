<?php

namespace App\Http\Controllers\apis\Admin;

use App\Http\Controllers\apis\ApiController;
use App\Http\Requests\Api\AdminRequest;
use App\Http\Resources\AdminResource;
use App\Models\Admin;
use App\Services\AdminService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Admin-namespaced endpoint serving the 2026 Controllers redesign.
 *
 * The legacy public endpoint (`/api/v1/admins` → AdminController) is
 * left untouched and continues to serve any other consumer. The new
 * endpoint reuses the existing AdminService + AdminRequest but
 * crucially wraps every response with AdminResource so the table can
 * render `role_chip` (color + bilingual name) without a second
 * round-trip.
 *
 * Routes:
 *   GET    /api/v1/admin/controllers
 *   POST   /api/v1/admin/controllers
 *   GET    /api/v1/admin/controllers/{admin}
 *   PUT    /api/v1/admin/controllers/{admin}
 *   DELETE /api/v1/admin/controllers/{admin}
 */
class AdminControllersController extends ApiController
{
    public function __construct(private readonly AdminService $service) {}

    /** GET /api/v1/admin/controllers */
    public function index(Request $request): JsonResponse
    {
        $admins = $this->service->paginate(
            (int) $request->get('per_page', 20),
            $request->get('search'),
        );

        // Wrap rows with AdminResource so `role_chip`, `roles`, etc. ride
        // along — the legacy /admins endpoint returns bare models, which
        // is why the Controllers redesign couldn't render role badges.
        return $this->paginated(__('messages.retrieved'), AdminResource::collection($admins));
    }

    /** GET /api/v1/admin/controllers/{admin} */
    public function show(Admin $admin): JsonResponse
    {
        $admin = $this->service->findWithRoles($admin->id);
        return $this->success(__('messages.retrieved'), new AdminResource($admin));
    }

    /** POST /api/v1/admin/controllers */
    public function store(AdminRequest $request): JsonResponse
    {
        $admin = $this->service->create($request->validated());
        return $this->created(__('messages.created'), new AdminResource($admin));
    }

    /** PUT /api/v1/admin/controllers/{admin} */
    public function update(AdminRequest $request, Admin $admin): JsonResponse
    {
        try {
            $admin = $this->service->update($admin, $request->validated());
            return $this->success(__('messages.updated'), new AdminResource($admin));
        } catch (ModelNotFoundException) {
            return $this->notFound();
        }
    }

    /** DELETE /api/v1/admin/controllers/{admin} */
    public function destroy(Admin $admin): JsonResponse
    {
        $this->service->delete($admin);
        return $this->success(__('messages.deleted'), null);
    }
}
