<?php

namespace App\Http\Controllers\apis\Admin;

use App\Http\Controllers\apis\ApiController;
use App\Http\Resources\Admin\AdminAuditLogResource;
use App\Services\Admin\AdminAuditLogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Admin endpoints powering the 2026 Audit Log redesign.
 *
 * Strictly additive — the legacy public read endpoint
 * (App\Http\Controllers\apis\AuditLogController @
 * /api/v1/audit-log) is left untouched and continues to serve its
 * original consumers.
 *
 * Routes:
 *   GET  /api/v1/admin/audit-log
 *   GET  /api/v1/admin/audit-log/filter-options
 *   GET  /api/v1/admin/audit-log/export
 */
class AdminAuditLogController extends ApiController
{
    public function __construct(private readonly AdminAuditLogService $service) {}

    /**
     * GET /api/v1/admin/audit-log
     *
     * Query params:
     *   - page, per_page
     *   - role             admin | instructor (omit / "all" → no filter)
     *   - search           free-text across actor / action / description / IP
     *   - date_from        YYYY-MM-DD inclusive
     *   - date_to          YYYY-MM-DD inclusive
     *   - instructor_ids[] sub-filter when role = instructor
     */
    public function index(Request $request): JsonResponse
    {
        $rows = $this->service->paginate(
            role:          $this->normaliseRole($request->input('role')),
            search:        $request->string('search')->toString() ?: null,
            dateFrom:      $request->string('date_from')->toString() ?: null,
            dateTo:        $request->string('date_to')->toString()   ?: null,
            instructorIds: $this->intArray($request->input('instructor_ids')),
            perPage:       (int) $request->get('per_page', AdminAuditLogService::PER_PAGE_DEFAULT),
        );

        return $this->paginated(
            __('messages.retrieved'),
            AdminAuditLogResource::collection($rows),
        );
    }

    /** GET /api/v1/admin/audit-log/filter-options */
    public function filterOptions(): JsonResponse
    {
        return $this->success(__('messages.retrieved'), $this->service->filterOptions());
    }

    /**
     * GET /api/v1/admin/audit-log/export
     *
     * Streams a UTF-8 BOM'd CSV that mirrors the on-screen filters so
     * the admin always exports exactly what they're looking at.
     */
    public function export(Request $request): StreamedResponse|JsonResponse
    {
        [$headings, $rows] = $this->service->dataset(
            role:          $this->normaliseRole($request->input('role')),
            search:        $request->string('search')->toString() ?: null,
            dateFrom:      $request->string('date_from')->toString() ?: null,
            dateTo:        $request->string('date_to')->toString()   ?: null,
            instructorIds: $this->intArray($request->input('instructor_ids')),
        );

        $filename = 'audit-log-' . now()->format('Ymd-His') . '.csv';

        return response()->streamDownload(function () use ($headings, $rows) {
            $out = fopen('php://output', 'w');
            // BOM so Excel opens the file as UTF-8.
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, $headings);
            foreach ($rows as $row) {
                fputcsv($out, $row);
            }
            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /* ------------------------------------------------------------------ *
     |  HELPERS                                                           |
     * ------------------------------------------------------------------ */

    private function normaliseRole(mixed $value): ?string
    {
        $value = is_string($value) ? strtolower(trim($value)) : null;
        return in_array($value, AdminAuditLogService::ROLES, true) ? $value : null;
    }

    /**
     * Accept either an array (`instructor_ids[]=1&instructor_ids[]=2`)
     * or a comma-joined string (`instructor_ids=1,2,3`).
     *
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
