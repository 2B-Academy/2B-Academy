<?php

namespace App\Http\Controllers\apis\Admin;

use App\Http\Controllers\apis\ApiController;
use App\Http\Resources\Admin\AdminRatingListResource;
use App\Services\Admin\AdminRatingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Admin endpoints for the Ratings overview defined by the 2026 Figma redesign.
 *
 * This controller is intentionally NEW and additive — the legacy controller
 * App\Http\Controllers\apis\CourseRatingController and its routes
 * (routes/apis/ratings.php) are left untouched so existing learner / admin
 * flows continue to work without modification.
 */
class AdminRatingController extends ApiController
{
    public function __construct(private readonly AdminRatingService $service) {}

    /**
     * GET /api/v1/admin/ratings
     *
     * Query params:
     *   - page, per_page
     *   - search           (matches learner name OR comment)
     *   - instructor_ids[] (multi-select)
     *   - learner_ids[]    (multi-select; user_id)
     *   - course_ids[]     (multi-select)
     *   - min_rating, max_rating (optional 1..5 bounds)
     */
    public function index(Request $request): JsonResponse
    {
        $ratings = $this->service->paginate(
            instructorIds: $this->intArray($request->input('instructor_ids')),
            learnerIds:    $this->intArray($request->input('learner_ids')),
            courseIds:     $this->intArray($request->input('course_ids')),
            search:        $request->string('search')->toString() ?: null,
            minRating:     $request->filled('min_rating') ? (int) $request->input('min_rating') : null,
            maxRating:     $request->filled('max_rating') ? (int) $request->input('max_rating') : null,
            perPage:       (int) $request->get('per_page', 20),
        );

        return $this->paginated(
            __('messages.retrieved'),
            AdminRatingListResource::collection($ratings),
        );
    }

    /**
     * GET /api/v1/admin/ratings/summary
     */
    public function summary(): JsonResponse
    {
        return $this->success(__('messages.retrieved'), $this->service->summary());
    }

    /**
     * GET /api/v1/admin/ratings/filter-options
     */
    public function filterOptions(): JsonResponse
    {
        return $this->success(__('messages.retrieved'), $this->service->filterOptions());
    }

    /* ------------------------------------------------------------------ *
     |  HELPERS                                                           |
     * ------------------------------------------------------------------ */

    /**
     * Normalise a possibly-CSV / array request value into a list of ints.
     *
     * @param  mixed  $value
     * @return array<int,int>|null
     */
    private function intArray($value): ?array
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
