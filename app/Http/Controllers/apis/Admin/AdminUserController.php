<?php

namespace App\Http\Controllers\apis\Admin;

use App\Http\Controllers\apis\ApiController;
use App\Http\Requests\Api\Admin\AdminUserStoreRequest;
use App\Http\Requests\Api\Admin\AdminUserUpdateRequest;
use App\Http\Resources\Admin\AdminUserDetailResource;
use App\Http\Resources\Admin\AdminUserListResource;
use App\Services\Admin\AdminUserService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Admin endpoints powering the 2026 Users overview redesign.
 *
 * The LMS keeps three sibling person-tables (users / instructors / admins)
 * which the Figma design unifies in a single list. This controller exposes
 * a composite resource keyed by (`source`, `id`) — the URL segment after
 * `users/` carries the source, which lets us dispatch each request to the
 * correct underlying table without touching any legacy controllers or
 * routes.
 *
 * Routes:
 *   GET    /admin/users
 *   POST   /admin/users
 *   GET    /admin/users/summary
 *   GET    /admin/users/filter-options
 *   GET    /admin/users/{source}/{id}
 *   PUT    /admin/users/{source}/{id}
 *   DELETE /admin/users/{source}/{id}
 *
 * Where {source} ∈ {user, instructor, admin}.
 */
class AdminUserController extends ApiController
{
    public function __construct(private readonly AdminUserService $service) {}

    /**
     * GET /api/v1/admin/users
     *
     * Query params:
     *   - page, per_page
     *   - role          (admin | instructor | learner)
     *   - status        (active | inactive | deactivated)
     *   - search        (matches name / email)
     *   - instructor_ids[]  (filter the Instructors pill by specific ids)
     */
    public function index(Request $request): JsonResponse
    {
        $role          = $this->normaliseRole($request->input('role'));
        $status        = $this->normaliseStatus($request->input('status'));
        $instructorIds = $this->intArray($request->input('instructor_ids'));

        $users = $this->service->paginate(
            role:          $role,
            status:        $status,
            search:        $request->string('search')->toString() ?: null,
            instructorIds: $instructorIds,
            perPage:       (int) $request->get('per_page', 15),
        );

        return $this->paginated(
            __('messages.retrieved'),
            AdminUserListResource::collection($users),
        );
    }

    /** GET /api/v1/admin/users/summary */
    public function summary(): JsonResponse
    {
        return $this->success(__('messages.retrieved'), $this->service->summary());
    }

    /** GET /api/v1/admin/users/filter-options */
    public function filterOptions(): JsonResponse
    {
        return $this->success(__('messages.retrieved'), $this->service->filterOptions());
    }

    /** GET /api/v1/admin/users/{source}/{id} */
    public function show(string $source, int $id): JsonResponse
    {
        $this->guardSource($source);

        try {
            $row = $this->service->show($source, $id);
        } catch (ModelNotFoundException) {
            return $this->notFound();
        } catch (\InvalidArgumentException) {
            return $this->error(__('messages.not_found'), 404);
        }

        return $this->success(__('messages.retrieved'), new AdminUserDetailResource($row));
    }

    /** POST /api/v1/admin/users */
    public function store(AdminUserStoreRequest $request): JsonResponse
    {
        $created = $this->service->create($request->validated());
        $row     = $this->service->show($created['source'], $created['id']);

        return $this->created(__('messages.created'), new AdminUserDetailResource($row));
    }

    /** PUT /api/v1/admin/users/{source}/{id} */
    public function update(AdminUserUpdateRequest $request, string $source, int $id): JsonResponse
    {
        $this->guardSource($source);

        try {
            $row = $this->service->update($source, $id, $request->validated());
        } catch (ModelNotFoundException) {
            return $this->notFound();
        } catch (\InvalidArgumentException) {
            return $this->error(__('messages.not_found'), 404);
        }

        return $this->success(__('messages.updated'), new AdminUserDetailResource($row));
    }

    /**
     * DELETE /api/v1/admin/users/{source}/{id}
     *
     * Soft-deactivates the row (status = 'deactivated'). No row is ever
     * removed from the underlying table so legacy relationships stay valid.
     */
    public function destroy(string $source, int $id): JsonResponse
    {
        $this->guardSource($source);

        try {
            $row = $this->service->deactivate($source, $id);
        } catch (ModelNotFoundException) {
            return $this->notFound();
        } catch (\InvalidArgumentException) {
            return $this->error(__('messages.not_found'), 404);
        }

        return $this->success(__('messages.updated'), new AdminUserDetailResource($row));
    }

    /**
     * PATCH /api/v1/admin/users/{source}/{id}/reactivate
     *
     * Reverses a soft-deactivation by setting `status = 'active'`.
     */
    public function reactivate(string $source, int $id): JsonResponse
    {
        $this->guardSource($source);

        try {
            $row = $this->service->reactivate($source, $id);
        } catch (ModelNotFoundException) {
            return $this->notFound();
        } catch (\InvalidArgumentException) {
            return $this->error(__('messages.not_found'), 404);
        }

        return $this->success(__('messages.updated'), new AdminUserDetailResource($row));
    }

    /* ------------------------------------------------------------------ *
     |  HELPERS                                                           |
     * ------------------------------------------------------------------ */

    private function guardSource(string $source): void
    {
        if (!in_array($source, AdminUserService::SOURCES, true)) {
            abort(404);
        }
    }

    private function normaliseRole(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $value = trim($value);
        if ($value === '' || $value === 'all') {
            return null;
        }

        // No allowlist here — the Add/Edit modal sources roles from the
        // dynamic `roles` table, so the dropdown can carry custom role
        // machine names (e.g. "reports-viewer"). AdminUserService scopes
        // the resulting query through the Spatie pivot, which is itself
        // a uniqueness boundary.
        return $value;
    }

    private function normaliseStatus(mixed $value): ?string
    {
        $value = is_string($value) ? strtolower(trim($value)) : null;
        return in_array($value, ['active', 'inactive', 'deactivated'], true) ? $value : null;
    }

    /**
     * @return array<int,int>|null
     */
    private function intArray(mixed $value): ?array
    {
        if ($value === null || $value === '') {
            return null;
        }

        $raw = is_array($value) ? $value : explode(',', (string) $value);
        $ids = array_values(array_filter(array_map(
            static fn ($v) => (int) $v,
            $raw,
        ), static fn (int $v) => $v > 0));

        return $ids ?: null;
    }
}
