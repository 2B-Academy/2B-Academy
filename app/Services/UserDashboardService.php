<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Course;
use App\Models\CourseLectureQuestion;
use App\Models\CourseRating;
use App\Models\User;
use App\Models\UserCourseAssignment;
use App\Models\UserCourseEvaluation;
use App\Models\UserExam;
use Illuminate\Support\Collection;

class UserDashboardService
{
    /**
     * Aggregate personal stats for the dashboard.
     * Mirrors DashboardController::getUserStatistics().
     */
    public function getStats(User $user): array
    {
        $userId = $user->id;

        $courses = Course::where('for_public', true)
            ->orWhereIn('id', fn ($q) => $q->select('course_id')->from('users_courses')->where('user_id', $userId))
            ->count();

        $exams       = UserExam::where('user_id', $userId)->count();
        $questions   = CourseLectureQuestion::where('user_id', $userId)->count();
        $certificates = $this->getCertificates($user)->count();
        $ratings     = CourseRating::where('user_id', $userId)->count();
        $yearHours   = Attendance::where('user_id', $userId)
            ->whereYear('created_at', date('Y'))
            ->sum('attendance_hours');

        return [
            'courses'      => $courses,
            'exams'        => $exams,
            'questions'    => $questions,
            'certificates' => $certificates,
            'ratings'      => $ratings,
            'year_hours'   => (float) $yearHours,
        ];
    }

    /** Enrolled + public courses for the user. */
    public function getMyCourses(User $user): \Illuminate\Database\Eloquent\Collection
    {
        $userId = $user->id;

        return Course::where('for_public', true)
            ->orWhereIn('id', fn ($q) => $q->select('course_id')->from('users_courses')->where('user_id', $userId))
            ->with(['category:id,name', 'usersCourses' => fn ($q) => $q->where('user_id', $userId)])
            ->withCount([
                'attendances as user_attendances_count' => fn ($q) => $q->where('user_id', $userId),
            ])
            ->latest()
            ->get();
    }

    /** Assignments (with submission status) for the user's accessible courses. */
    public function getMyAssignments(User $user): array
    {
        $userId = $user->id;

        $courses = Course::whereHas('assignments')
            ->where(fn ($q) => $q->where('for_public', true)
                ->orWhereIn('id', fn ($sub) => $sub->select('course_id')->from('users_courses')->where('user_id', $userId))
            )
            ->with(['assignments'])
            ->latest()
            ->get();

        $assignments = [];
        foreach ($courses as $course) {
            foreach ($course->assignments as $assignment) {
                $submission = UserCourseAssignment::where('user_id', $userId)
                    ->where('course_assignment_id', $assignment->id)
                    ->first();

                $assignments[] = [
                    'assignment_id'   => $assignment->id,
                    'course_id'       => $course->id,
                    'course_title'    => $course->getTranslation('title', app()->getLocale()),
                    'title'           => $assignment->getTranslation('title', app()->getLocale()),
                    'assignment_file' => $assignment->file,
                    'submitted'       => (bool) $submission,
                    'user_file'       => $submission?->user_file,
                    'feedback'        => $submission?->feedback,
                    'score'           => $submission?->score,
                ];
            }
        }

        return $assignments;
    }

    /**
     * Earned certificates for the user.
     * Mirrors HelperTrait::userCertificates().
     */
    public function getCertificates(User $user): \Illuminate\Support\Collection
    {
        $userId = $user->id;

        $examCertificates = $user->exams()
            ->with(['course:id,title,certificate,title_for_certificate,is_evaluate', 'exam:id,title,degree,is_final'])
            ->whereHas('course', fn ($q) => $q->where('certificate', true))
            ->whereHas('exam', fn ($q) => $q->where('is_final', true))
            ->where('status', 'success')
            ->get();

        $evaluationCerts = UserCourseEvaluation::with('course:id,title,is_evaluate')
            ->where('user_id', $userId)
            ->get();

        $certificates = collect();

        foreach ($examCertificates as $exam) {
            if (!$exam->course) continue;
            if (!$exam->course->is_evaluate) {
                $certificates->push(['type' => 'exam', 'data' => $exam]);
            }
        }

        foreach ($evaluationCerts as $evaluation) {
            $course = $evaluation->course;
            if (!$course || !$course->is_evaluate) continue;

            $alreadyAdded = $certificates->contains(fn ($c) => $c['data']->course?->id === $course->id);
            if (!$alreadyAdded) {
                $certificates->push(['type' => 'evaluation', 'data' => $evaluation]);
            }
        }

        return $certificates;
    }

    /** User's own course ratings with course title. */
    public function getMyRatings(int $userId): Collection
    {
        return CourseRating::where('user_id', $userId)
            ->with(['course:id,title'])
            ->latest()
            ->get()
            ->map(fn ($r) => [
                'id'         => $r->id,
                'rating'     => $r->rating,
                'review'     => $r->review,
                'course_id'  => $r->course_id,
                'course'     => $r->course
                    ? $r->course->getTranslation('title', app()->getLocale())
                    : null,
                'created_at' => $r->created_at?->toDateTimeString(),
            ]);
    }

    /** User's own lecture questions with course + lecture context. */
    public function getMyLectureQuestions(int $userId): Collection
    {
        return CourseLectureQuestion::where('user_id', $userId)
            ->with(['course:id,title', 'lecture:id,title'])
            ->latest()
            ->get()
            ->map(fn ($q) => [
                'id'          => $q->id,
                'question'    => $q->question,
                'answer'      => $q->answer,
                'is_answered' => !is_null($q->answer),
                'course_id'   => $q->course_id,
                'course'      => $q->course
                    ? $q->course->getTranslation('title', app()->getLocale())
                    : null,
                'lecture_id'  => $q->lecture_id,
                'lecture'     => $q->lecture
                    ? $q->lecture->getTranslation('title', app()->getLocale())
                    : null,
                'created_at'  => $q->created_at?->toDateTimeString(),
            ]);
    }

    /** User's attendance records for a specific course. */
    public function getUserCourseAttendance(int $userId, int $courseId): array
    {
        $records = Attendance::where('user_id', $userId)
            ->where('course_id', $courseId)
            ->orderBy('created_at')
            ->get();

        return [
            'course_id'        => $courseId,
            'total_hours'      => (float) $records->sum('attendance_hours'),
            'sessions_attended' => $records->count(),
            'records'          => $records->map(fn ($a) => [
                'id'               => $a->id,
                'attendance_hours' => (float) $a->attendance_hours,
                'is_manual'        => (bool) $a->is_manual,
                'date'             => $a->created_at?->toDateString(),
            ])->values(),
        ];
    }
}
