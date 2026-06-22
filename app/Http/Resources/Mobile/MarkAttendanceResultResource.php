<?php

declare(strict_types=1);

namespace App\Http\Resources\Mobile;

use App\Enums\Mobile\AttendanceMarkFailure;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * S-06 result payload — both for success (HTTP 201) and failure
 * (HTTP 4xx). The shape is identical so the mobile client can use a
 * single decoder regardless of outcome.
 *
 * Carries a `learner` identity block (with `machine_code` — the
 * HR-sourced business id) so the mobile success snackbar can show
 * "✓ Marked present — <machine_code> · <name>" and the user can
 * verify that the attendance was logged under their HR identity.
 */
class MarkAttendanceResultResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $row = $this->resource;
        $failure = $row['failure'] ?? null;

        return [
            'success'        => (bool) $row['success'],
            'attendance_id'  => $row['attendance_id'] ?? null,
            'failure'        => $failure instanceof AttendanceMarkFailure ? $failure->value : null,
            'failure_key'    => $failure instanceof AttendanceMarkFailure ? $failure->messageKey() : null,
            // The machine_code as actually persisted on the attendance
            // row (echoed back so the mobile client can prove the HR
            // identity that was logged).
            'persisted_machine_code' => $row['learner_machine_code'] ?? null,
            'learner'        => $request->user()
                ? (new LearnerIdentityResource($request->user()))->toArray($request)
                : null,
            'session'        => $row['session'] === null ? null : [
                'id'                  => (int) ($row['session']->id ?? 0),
                'course_id'           => (int) ($row['session']->course_id ?? 0),
                'title'               => $row['session']->title ?? null,
                'session_date'        => $row['session']->session_date ?? null,
                'time_from'           => $row['session']->time_from ?? null,
                'time_to'             => $row['session']->time_to ?? null,
                'passcode_expires_at' => $row['session']->passcode_expires_at ?? null,
            ],
        ];
    }
}
