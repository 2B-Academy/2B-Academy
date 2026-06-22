<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Requests\Api\Mobile\MarkAttendanceRequest;
use App\Http\Resources\Mobile\MarkAttendanceResultResource;
use App\Models\Course;
use App\Services\Mobile\MobileAttendanceService;
use Illuminate\Http\JsonResponse;

/**
 * Mobile S-06 — Mark Present (passcode flow).
 *
 * 📱 MOBILE — Employee/Learner mobile app. Grouped under the single
 * `Mobile` Swagger tag (registered globally in App\OpenApi\Info).
 *
 * The flow is intentionally single-purpose. Status mapping:
 *   201 — success
 *   200 — wrong passcode (recoverable; body has success:false)
 *   403 — not enrolled in the course
 *   409 — no open session window OR already marked attended
 *   422 — expired passcode
 */
class AttendanceController extends MobileBaseController
{
    public function __construct(private readonly MobileAttendanceService $attendance) {}

    /**
     * @OA\Post(
     *     path="/mobile/attendance/mark",
     *     tags={"Mobile"},
     *     summary="📱 [MOBILE · S-06] Submit session passcode",
     *     description="📱 **MOBILE** · Screen **S-06 — Mark Present** · Audience: Employee/Learner mobile app · Validates enrolment, open session window, passcode match, expiry, and duplicate prevention; writes a denormalized attendance row + audit log.",
     *     @OA\Parameter(ref="#/components/parameters/MobileAuthorization"),
     *     @OA\Parameter(ref="#/components/parameters/EmployeeCode"),
     *     @OA\Parameter(ref="#/components/parameters/AcceptLanguage"),
     *     @OA\RequestBody(required=true, @OA\JsonContent(
     *         required={"course_id","passcode"},
     *         @OA\Property(property="course_id",  type="integer"),
     *         @OA\Property(property="session_id", type="integer", nullable=true),
     *         @OA\Property(property="passcode",   type="string"),
     *     )),
     *     @OA\Response(response=201, description="Attendance recorded"),
     *     @OA\Response(response=200, description="Wrong passcode (success:false)"),
     *     @OA\Response(response=403, description="Not enrolled"),
     *     @OA\Response(response=409, description="No open window / already marked"),
     *     @OA\Response(response=422, description="Expired code")
     * )
     */
    public function mark(MarkAttendanceRequest $request): JsonResponse
    {
        $course = Course::findOrFail((int) $request->validated('course_id'));

        $result = $this->attendance->markPresent(
            user: $request->user(),
            course: $course,
            sessionId: $request->validated('session_id'),
            passcode: (string) $request->validated('passcode'),
        );

        if ($result['success']) {
            return response()->json([
                'status'  => 'success',
                'message' => __('messages.mobile.attendance_marked'),
                'result'  => new MarkAttendanceResultResource($result),
            ], 201);
        }

        $failure = $result['failure'];
        return response()->json([
            'status'  => 'error',
            'message' => __($failure->messageKey()),
            'result'  => new MarkAttendanceResultResource($result),
        ], $failure->httpStatus());
    }
}
