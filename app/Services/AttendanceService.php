<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\AttendanceLog;
use App\Models\Course;
use App\Models\User;
use App\Models\UsersCourse;
use App\Repositories\Contracts\AttendanceRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class AttendanceService
{
    public function __construct(
        private readonly AttendanceRepositoryInterface $attendanceRepository,
    ) {}

    public function paginate(int $perPage, array $filters): LengthAwarePaginator
    {
        return $this->attendanceRepository->paginateFiltered($perPage, $filters);
    }

    /**
     * Record one attendance session for a user on a course.
     * Mirrors the legacy saveAttendance() helper exactly.
     *
     * @param int|null $sessionId  Optional course_sessions.id this attendance
     *                             is for. When provided we pin the row to that
     *                             exact session so the Cohort Attendance drawer
     *                             can group per-session unambiguously even when
     *                             two sessions land on the same calendar day.
     *                             Backward-compatible: existing callers that
     *                             don't pass it keep the old date-match behaviour.
     */
    public function record(User $user, Course $course, ?int $sessionId = null): array
    {
        $course->loadMissing('category');

        $userGroupId = UsersCourse::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->value('group_id') ?? 0;

        $sessionsCount     = $course->sessions()->where('section_id', $userGroupId)->count();
        $userSessionsCount = $sessionsCount > 0 ? $sessionsCount : 1;

        $userAttendanceCount = $this->attendanceRepository->countForUserInSection($user->id, $userGroupId)
            ?: Attendance::where('user_id', $user->id)->where('course_id', $course->id)->count();

        if ($userAttendanceCount >= $userSessionsCount) {
            return ['success' => false, 'message' => __('messages.attendance_complete')];
        }

        $attendanceHours = $userSessionsCount > 1
            ? round($course->hours / $userSessionsCount, 2)
            : (float) $course->hours;

        DB::table('attendances')->insert([
            'user_id'              => $user->id,
            'user_machine_code'    => $user->machine_code,
            'user_department'      => $user->department_name,
            'course_category_id'   => $course->category?->id,
            'course_category_name' => $course->category?->name,
            'course_id'            => $course->id,
            'course_name'          => $course->title,
            'course_hours'         => $course->hours,
            'section_id'           => $userGroupId,
            'session_id'           => $sessionId,
            'attendance_hours'     => $attendanceHours,
            'is_manual'            => true,
            'created_at'           => now(),
            'updated_at'           => now(),
        ]);

        return ['success' => true, 'message' => __('messages.attendance_recorded')];
    }

    /**
     * Remove the latest attendance record for a user on a course and log it.
     */
    public function remove(User $user, Course $course, int $adminId): array
    {
        $attendance = Attendance::where(['user_id' => $user->id, 'course_id' => $course->id])
            ->latest()
            ->first();

        if (!$attendance) {
            return ['success' => false, 'message' => __('messages.not_found')];
        }

        AttendanceLog::create([
            'attendance_id' => $attendance->id,
            'user_id'       => $adminId,
            'employee_code' => $user->machine_code,
            'log'           => "تم حذف سيشن للموظف {$user->id} والدورة التدريبية {$course->id}",
        ]);

        $attendance->delete();

        return ['success' => true, 'message' => __('messages.deleted')];
    }
}
