<?php

declare(strict_types=1);

namespace App\Http\Controllers\apis\Learner;

use App\Http\Controllers\apis\ApiController;
use App\Http\Resources\Mobile\MarkAttendanceResultResource;
use App\Models\Course;
use App\Services\Mobile\MobileAttendanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * 🌐 LEARNER WEB — "Mark as Present" (passcode flow), the browser twin of the
 * mobile S-06 screen. Reuses MobileAttendanceService::markPresent verbatim;
 * the only difference from /mobile/attendance/mark is auth — here the learner
 * is the per-user Sanctum principal ($request->user()) instead of the S2S
 * mobile token + Employee-Code header.
 */
final class AttendanceController extends ApiController
{
    public function __construct(private readonly MobileAttendanceService $attendance) {}

    /** POST learner/profile/attendance/mark  { course_id, session_id?, passcode } */
    public function mark(Request $request): JsonResponse
    {
        $data = $request->validate([
            'course_id'  => ['required', 'integer', 'min:1', 'exists:courses,id'],
            'session_id' => ['nullable', 'integer', 'min:1', 'exists:course_sessions,id'],
            'passcode'   => ['required', 'string', 'max:12', 'regex:/^[0-9]+$/'],
        ]);

        $course = Course::findOrFail((int) $data['course_id']);

        $result = $this->attendance->markPresent(
            user: $request->user(),
            course: $course,
            sessionId: isset($data['session_id']) ? (int) $data['session_id'] : null,
            passcode: (string) $data['passcode'],
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
