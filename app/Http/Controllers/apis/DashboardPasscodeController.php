<?php

declare(strict_types=1);

namespace App\Http\Controllers\apis;

use App\Http\Requests\Api\Mobile\StartSessionPasscodeRequest;
use App\Http\Resources\SessionPasscodeStateResource;
use App\Models\Instructor;
use App\Services\DashboardPasscodeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use OpenApi\Annotations as OA;

/**
 * Instructor-dashboard "Passcode" widget (Figma 515:34995 / 515:37969 /
 * 515:35489). Read the current live-session passcode state, and
 * generate a passcode for the current live session in one tap — without
 * the dashboard having to know a session id up-front.
 *
 * ADDITIVE: delegates real generation to the existing
 * SessionPasscodeService (same as POST /admin/course-sessions/{session}
 * /passcode) so the dashboard and mobile S-06 flow never diverge.
 */
class DashboardPasscodeController extends ApiController
{
    public function __construct(private readonly DashboardPasscodeService $service) {}

    /**
     * @OA\Get(
     *     path="/dashboard/passcode",
     *     tags={"Dashboard"},
     *     summary="Current instructor-dashboard passcode widget state.",
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(ref="#/components/parameters/AcceptLanguage"),
     *     @OA\Response(
     *         response=200,
     *         description="Passcode widget state",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(@OA\Property(property="result", ref="#/components/schemas/SessionPasscodeState"))
     *             }
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden")
     * )
     */
    public function current(Request $request): JsonResponse
    {
        return $this->success(
            __('messages.retrieved'),
            new SessionPasscodeStateResource(
                $this->service->currentState($this->instructorCourseIds($request)),
            ),
        );
    }

    /**
     * @OA\Get(
     *     path="/dashboard/passcode/courses",
     *     tags={"Dashboard"},
     *     summary="Courses (+ cohorts) the instructor can start a session for.",
     *     description="Excludes courses whose cohorts have all ended. Each course carries its still-runnable cohorts so the dashboard can render the course → cohort pickers.",
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(ref="#/components/parameters/AcceptLanguage"),
     *     @OA\Response(response=200, description="Eligible courses"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden")
     * )
     */
    public function courses(Request $request): JsonResponse
    {
        $courseIds = $this->instructorCourseIds($request);

        if ($courseIds === null) {
            return $this->error(__('messages.forbidden'), 403);
        }

        return $this->success(
            __('messages.retrieved'),
            $this->service->eligibleCourses($courseIds),
        );
    }

    /**
     * @OA\Post(
     *     path="/dashboard/passcode",
     *     tags={"Dashboard"},
     *     summary="Start a session for the chosen course/cohort and issue its passcode.",
     *     description="Creates a `course_sessions` row for today (starting now for the configured attendance window) on the selected course + cohort, then issues a numeric passcode that powers the mobile S-06 Mark Present screen.",
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(ref="#/components/parameters/AcceptLanguage"),
     *     @OA\RequestBody(required=true, @OA\JsonContent(
     *         required={"course_id","cohort_id"},
     *         @OA\Property(property="course_id",  type="integer"),
     *         @OA\Property(property="cohort_id",  type="integer"),
     *         @OA\Property(property="expires_at", type="string", format="date-time", nullable=true),
     *         @OA\Property(property="length",     type="integer", nullable=true)
     *     )),
     *     @OA\Response(
     *         response=200,
     *         description="Session started + passcode generated",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(@OA\Property(property="result", ref="#/components/schemas/SessionPasscodeState"))
     *             }
     *         )
     *     ),
     *     @OA\Response(response=422, description="Cohort is not a valid target (wrong course or already ended)."),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden")
     * )
     */
    public function generate(StartSessionPasscodeRequest $request): JsonResponse
    {
        $courseIds = $this->instructorCourseIds($request);

        // Only instructors who teach can generate a passcode.
        if ($courseIds === null) {
            return $this->error(__('messages.forbidden'), 403);
        }

        $courseId = (int) $request->validated('course_id');
        $cohortId = (int) $request->validated('cohort_id');

        // The course must be one the authenticated instructor teaches.
        if (! in_array($courseId, $courseIds, true)) {
            return $this->error(__('messages.forbidden'), 403);
        }

        // The cohort must belong to that course and still be runnable.
        if ($this->service->findEligibleCohort($courseId, $cohortId) === null) {
            return $this->error(__('messages.passcode.cohort_unavailable'), 422);
        }

        $expiresAt = $request->filled('expires_at')
            ? Carbon::parse((string) $request->validated('expires_at'))
            : null;

        return $this->success(
            __('messages.passcode.session_started'),
            new SessionPasscodeStateResource(
                $this->service->startSessionAndIssue(
                    $courseId,
                    $cohortId,
                    $request->validated('length'),
                    $expiresAt,
                ),
            ),
        );
    }

    /**
     * @OA\Post(
     *     path="/dashboard/passcode/regenerate",
     *     tags={"Dashboard"},
     *     summary="Rotate (re-issue) the passcode on the instructor's live session.",
     *     description="Issues a fresh code on the session whose attendance window is currently open — powering the dashboard 'Regenerate' button and the rotating-passcode auto-refresh. Returns the read-only current state (ended/idle) when nothing is live to rotate.",
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(ref="#/components/parameters/AcceptLanguage"),
     *     @OA\Response(
     *         response=200,
     *         description="Passcode rotated (or current state when nothing is live)",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(@OA\Property(property="result", ref="#/components/schemas/SessionPasscodeState"))
     *             }
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden")
     * )
     */
    public function regenerate(Request $request): JsonResponse
    {
        $courseIds = $this->instructorCourseIds($request);

        if ($courseIds === null) {
            return $this->error(__('messages.forbidden'), 403);
        }

        return $this->success(
            __('messages.passcode.session_started'),
            new SessionPasscodeStateResource(
                $this->service->regenerateCurrent($courseIds),
            ),
        );
    }

    /**
     * @OA\Post(
     *     path="/dashboard/passcode/end",
     *     tags={"Dashboard"},
     *     summary="End the instructor's current live session.",
     *     description="Revokes the live passcode and closes the attendance window now, so the session stops being live and the rotating-passcode auto-refresh halts. Returns the refreshed (idle) widget state.",
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(ref="#/components/parameters/AcceptLanguage"),
     *     @OA\Response(
     *         response=200,
     *         description="Session ended",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(@OA\Property(property="result", ref="#/components/schemas/SessionPasscodeState"))
     *             }
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden")
     * )
     */
    public function end(Request $request): JsonResponse
    {
        $courseIds = $this->instructorCourseIds($request);

        if ($courseIds === null) {
            return $this->error(__('messages.forbidden'), 403);
        }

        return $this->success(
            __('messages.passcode.session_ended'),
            new SessionPasscodeStateResource(
                $this->service->endCurrent($courseIds),
            ),
        );
    }

    /**
     * Resolve the courses the authenticated back-office user teaches.
     *
     * Instructors are matched to the `instructors` catalogue by email
     * (the unified admin Users view keeps the two personas in sync). A
     * `null` return means "not an instructor / teaches nothing" — the
     * widget is then hidden and generation is forbidden.
     *
     * @return array<int, int>|null
     */
    private function instructorCourseIds(Request $request): ?array
    {
        $user = $request->user();

        if ($user === null || empty($user->email)) {
            return null;
        }

        $instructor = Instructor::query()
            ->where('email', $user->email)
            ->first();

        if ($instructor === null) {
            return null;
        }

        $ids = $instructor->courses()->pluck('courses.id')->all();

        return empty($ids) ? null : array_map('intval', $ids);
    }
}
