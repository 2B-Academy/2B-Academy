<?php

namespace App\Http\Controllers\apis;

use App\Http\Resources\CohortAttendanceResource;
use App\Models\Course;
use App\Models\CourseSection;
use App\Services\CohortAttendanceService;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

/**
 * Cohort Attendance — drives the "Attendance Record" drawer on the course
 * detail screen (see Figma nodes 454:42768, 332:11156, 454:38012, 454:38946).
 *
 * One endpoint returns the full rollup so the drawer can render its two
 * tabs (Sessions / Learners) and three filter chips (All / Presence /
 * Absence) without any extra round-trips.
 */
class CohortAttendanceController extends ApiController
{
    public function __construct(
        private readonly CohortAttendanceService $service,
    ) {}

    /**
     * @OA\Get(
     *     path="/courses/{course}/cohorts/{cohort}/attendance",
     *     tags={"Cohort Attendance"},
     *     summary="Admin: full attendance rollup for one cohort (sessions + learners + per-session absentees).",
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(ref="#/components/parameters/AcceptLanguage"),
     *     @OA\Parameter(
     *         name="course", in="path", required=true,
     *         description="Course identifier",
     *         @OA\Schema(type="integer", minimum=1)
     *     ),
     *     @OA\Parameter(
     *         name="cohort", in="path", required=true,
     *         description="Cohort (course_sections.id) identifier",
     *         @OA\Schema(type="integer", minimum=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Cohort attendance rollup",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(@OA\Property(property="result", ref="#/components/schemas/CohortAttendance"))
     *             }
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound")
     * )
     */
    public function show(Course $course, CourseSection $cohort): JsonResponse
    {
        // Defensive 404: a cohort id from a different course must not leak
        // attendance from this course (route-model binding by itself does
        // not enforce the parent–child link).
        abort_if($cohort->course_id !== $course->id, 404);

        return $this->success(
            __('messages.retrieved'),
            new CohortAttendanceResource($this->service->build($course, $cohort)),
        );
    }
}
