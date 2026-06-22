<?php

declare(strict_types=1);

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Mobile\IssuePasscodeRequest;
use App\Http\Traits\ApiResponse;
use App\Models\CourseSession;
use App\Services\Mobile\SessionPasscodeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;

/**
 * Admin-side passcode lifecycle for a single CourseSession. This is
 * the only mutating mobile-related endpoint exposed to admins. The
 * mobile user app never hits this — it only ever reads the projected
 * passcode out of a session.
 *
 * 🛠️ MOBILE FLOW (admin support) — drives mobile S-06 Mark Present.
 * The `Admin - Session Passcode` tag is registered globally in
 * App\OpenApi\Info.
 */
class SessionPasscodeController extends Controller
{
    use ApiResponse;

    public function __construct(private readonly SessionPasscodeService $service) {}

    /**
     * @OA\Post(
     *     path="/admin/course-sessions/{session}/passcode",
     *     tags={"Admin - Session Passcode"},
     *     security={{"BearerAuth":{}}},
     *     summary="🛠️ [MOBILE-ADMIN] Issue / rotate passcode (drives S-06)",
     *     description="🛠️ **MOBILE FLOW (admin support)** · Drives mobile **S-06 — Mark Present** · Audience: Instructors / admins · Generates a numeric passcode honoring `mobile_attendance.attendance_passcode_length` and `attendance_window_minutes`.",
     *     @OA\Parameter(ref="#/components/parameters/AcceptLanguage"),
     *     @OA\Parameter(name="session", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(required=false, @OA\JsonContent(
     *         @OA\Property(property="expires_at", type="string", format="date-time", nullable=true),
     *         @OA\Property(property="length",     type="integer", nullable=true),
     *     )),
     *     @OA\Response(response=200, description="OK")
     * )
     */
    public function issue(IssuePasscodeRequest $request, int $session): JsonResponse
    {
        $sessionModel = CourseSession::findOrFail($session);

        $expiresAt = $request->filled('expires_at')
            ? Carbon::parse((string) $request->validated('expires_at'))
            : null;

        $fresh = $this->service->issue(
            $sessionModel,
            $request->validated('length'),
            $expiresAt,
        );

        return $this->success(
            __('messages.updated'),
            [
                'session_id'           => (int) $fresh->id,
                'passcode'             => $fresh->passcode,
                'passcode_issued_at'   => $fresh->passcode_issued_at,
                'passcode_expires_at'  => $fresh->passcode_expires_at,
                'attendance_window_minutes' => $fresh->attendance_window_minutes,
            ],
        );
    }

    /**
     * @OA\Delete(
     *     path="/admin/course-sessions/{session}/passcode",
     *     tags={"Admin - Session Passcode"},
     *     security={{"BearerAuth":{}}},
     *     summary="🛠️ [MOBILE-ADMIN] Revoke session passcode (closes S-06 window)",
     *     description="🛠️ **MOBILE FLOW (admin support)** · Drives mobile **S-06 — Mark Present** · Audience: Instructors / admins · Closes the attendance window early by nulling out the stored passcode + expiry.",
     *     @OA\Parameter(ref="#/components/parameters/AcceptLanguage"),
     *     @OA\Parameter(name="session", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="OK")
     * )
     */
    public function revoke(int $session): JsonResponse
    {
        $sessionModel = CourseSession::findOrFail($session);
        $this->service->revoke($sessionModel);

        return $this->success(__('messages.deleted'));
    }
}
