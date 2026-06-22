<?php

namespace App\Http\Controllers\apis;

use App\Http\Requests\Api\RecordAttendanceRequest;
use App\Http\Resources\AttendanceResource;
use App\Models\Course;
use App\Models\User;
use App\Services\AttendanceService;
use App\Services\UserDashboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class AttendanceController extends ApiController
{
    public function __construct(
        private readonly AttendanceService      $attendanceService,
        private readonly UserDashboardService   $dashboardService,
    ) {}

    /**
     * @OA\Get(
     *     path="/attendance",
     *     tags={"Attendance"},
     *     summary="Admin: paginated attendance list with optional filters.",
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(ref="#/components/parameters/AcceptLanguage"),
     *     @OA\Parameter(ref="#/components/parameters/Page"),
     *     @OA\Parameter(ref="#/components/parameters/PerPage"),
     *     @OA\Parameter(
     *         name="course_id",
     *         in="query",
     *         required=false,
     *         description="Filter by course id.",
     *         @OA\Schema(type="integer", minimum=1)
     *     ),
     *     @OA\Parameter(
     *         name="user_id",
     *         in="query",
     *         required=false,
     *         description="Filter by user id.",
     *         @OA\Schema(type="integer", minimum=1)
     *     ),
     *     @OA\Parameter(
     *         name="section_id",
     *         in="query",
     *         required=false,
     *         description="Filter by course section (group) id.",
     *         @OA\Schema(type="integer", minimum=1)
     *     ),
     *     @OA\Parameter(
     *         name="from",
     *         in="query",
     *         required=false,
     *         description="Lower bound date (inclusive) for attended_at.",
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="to",
     *         in="query",
     *         required=false,
     *         description="Upper bound date (inclusive) for attended_at.",
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Paginated attendance records",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(@OA\Property(
     *                     property="result",
     *                     type="array",
     *                     @OA\Items(ref="#/components/schemas/Attendance")
     *                 ))
     *             }
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden")
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $filters = array_filter([
            'course_id'  => $request->get('course_id'),
            'user_id'    => $request->get('user_id'),
            'section_id' => $request->get('section_id'),
            'from'       => $request->get('from'),
            'to'         => $request->get('to'),
        ]);

        $attendance = $this->attendanceService->paginate(
            (int) $request->get('per_page', 20),
            $filters,
        );

        return $this->paginated(__('messages.retrieved'), AttendanceResource::collection($attendance));
    }

    /**
     * @OA\Post(
     *     path="/attendance",
     *     tags={"Attendance"},
     *     summary="Admin: manually record (status=1) or remove (status=0) an attendance session for a user/course pair.",
     *     security={{"BearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"user_id","course_id","status"},
     *             @OA\Property(property="user_id",   type="integer", example=42),
     *             @OA\Property(property="course_id", type="integer", example=7),
     *             @OA\Property(property="status",    type="boolean", description="true = record attendance, false = remove attendance.")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Attendance recorded or removed", @OA\JsonContent(ref="#/components/schemas/SuccessResponse")),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=422, ref="#/components/responses/ValidationError")
     * )
     */
    public function store(RecordAttendanceRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $user   = User::findOrFail($validated['user_id']);
        $course = Course::with('category')->findOrFail($validated['course_id']);

        if ($validated['status']) {
            $result = $this->attendanceService->record($user, $course);
        } else {
            $result = $this->attendanceService->remove($user, $course, $request->user()->id);
        }

        if (!$result['success']) {
            return $this->error($result['message'], 422);
        }

        return $this->success($result['message']);
    }

    /**
     * @OA\Get(
     *     path="/courses/{course}/my-attendance",
     *     tags={"Attendance"},
     *     summary="User: list own attendance records for a specific course.",
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(ref="#/components/parameters/AcceptLanguage"),
     *     @OA\Parameter(
     *         name="course",
     *         in="path",
     *         required=true,
     *         description="Course identifier",
     *         @OA\Schema(type="integer", minimum=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User's attendance records for the course",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(@OA\Property(
     *                     property="result",
     *                     type="array",
     *                     @OA\Items(ref="#/components/schemas/Attendance")
     *                 ))
     *             }
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound")
     * )
     */
    public function myCourseAttendance(Request $request, Course $course): JsonResponse
    {
        return $this->success(
            __('messages.retrieved'),
            $this->dashboardService->getUserCourseAttendance($request->user()->id, $course->id),
        );
    }
}
