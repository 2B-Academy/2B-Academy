<?php

namespace App\Http\Controllers\apis;

use App\Http\Requests\Api\CourseEnrollmentRequest;
use App\Models\Course;
use App\Models\UsersCourse;
use App\Services\UserEnrollmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class UserEnrollmentController extends ApiController
{
    public function __construct(private readonly UserEnrollmentService $service) {}

    /**
     * @OA\Get(
     *     path="/courses/{course}/enrollments",
     *     tags={"User Enrollment"},
     *     summary="Admin: paginated list of offline enrollments for a course (optionally filtered by group).",
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(ref="#/components/parameters/AcceptLanguage"),
     *     @OA\Parameter(ref="#/components/parameters/Page"),
     *     @OA\Parameter(ref="#/components/parameters/PerPage"),
     *     @OA\Parameter(
     *         name="course",
     *         in="path",
     *         required=true,
     *         description="Course identifier",
     *         @OA\Schema(type="integer", minimum=1)
     *     ),
     *     @OA\Parameter(
     *         name="group_id",
     *         in="query",
     *         required=false,
     *         description="Filter enrollments by course section (group) id.",
     *         @OA\Schema(type="integer", minimum=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Paginated offline enrollments",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(@OA\Property(
     *                     property="result",
     *                     type="array",
     *                     @OA\Items(ref="#/components/schemas/OfflineEnrollment")
     *                 ))
     *             }
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound")
     * )
     */
    public function index(Course $course, Request $request): JsonResponse
    {
        $enrollments = $this->service->paginate(
            $course,
            (int) $request->get('per_page', 20),
            $request->get('group_id') ? (int) $request->get('group_id') : null
        );
        return $this->paginated(__('messages.retrieved'), $enrollments);
    }

    /**
     * @OA\Post(
     *     path="/courses/{course}/enrollments",
     *     tags={"User Enrollment"},
     *     summary="Admin: enroll one or more users into an offline course section (group).",
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(
     *         name="course",
     *         in="path",
     *         required=true,
     *         description="Course identifier",
     *         @OA\Schema(type="integer", minimum=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"group_id","user_ids"},
     *             @OA\Property(property="group_id",   type="integer", example=3, description="Course section (group) id."),
     *             @OA\Property(
     *                 property="user_ids",
     *                 type="array",
     *                 minItems=1,
     *                 @OA\Items(type="integer", example=42)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Users enrolled",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(@OA\Property(
     *                     property="result",
     *                     type="object",
     *                     @OA\Property(property="enrolled", type="integer", example=3, description="Number of users actually enrolled.")
     *                 ))
     *             }
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=422, ref="#/components/responses/ValidationError")
     * )
     */
    public function store(Course $course, CourseEnrollmentRequest $request): JsonResponse
    {
        $data    = $request->validated();
        $enrolled = $this->service->enroll($course, $data['user_ids'], $data['group_id'] ?? null);
        return $this->success(__('messages.created'), ['enrolled' => $enrolled]);
    }

    /**
     * @OA\Delete(
     *     path="/courses/{course}/enrollments/{enrollment}",
     *     tags={"User Enrollment"},
     *     summary="Admin: remove a single offline enrollment from a course.",
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(
     *         name="course",
     *         in="path",
     *         required=true,
     *         description="Course identifier",
     *         @OA\Schema(type="integer", minimum=1)
     *     ),
     *     @OA\Parameter(
     *         name="enrollment",
     *         in="path",
     *         required=true,
     *         description="Enrollment (users_courses) identifier",
     *         @OA\Schema(type="integer", minimum=1)
     *     ),
     *     @OA\Response(response=200, description="Enrollment removed", @OA\JsonContent(ref="#/components/schemas/EmptyResponse")),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound")
     * )
     */
    public function destroy(Course $course, UsersCourse $enrollment): JsonResponse
    {
        abort_if($enrollment->course_id !== $course->id, 404);
        $this->service->remove($enrollment);
        return $this->deleted(__('messages.deleted'));
    }
}
