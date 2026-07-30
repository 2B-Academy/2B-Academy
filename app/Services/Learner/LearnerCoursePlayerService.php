<?php

namespace App\Services\Learner;

use App\Models\Course;
use App\Models\CourseAssignment;
use App\Models\CourseAssignmentQuestion;
use App\Models\CourseExam;
use App\Models\CourseExamQuestion;
use App\Models\CourseLecture;
use App\Models\CourseSection;
use App\Models\User;
use App\Models\UserCourseAssignment;
use App\Models\UserExam;
use App\Models\UserLectureProgress;
use App\Services\CertificateProjectionService;
use App\Services\LectureProgressService;

/**
 * Composite payload backing the course-player workspace's sidebar: lectures
 * (video/document/article/link) and rich quizzes/assignments, grouped into
 * the "Week 1: CX Foundations"-style sections the Figma sidebar renders,
 * plus the persistent certificate-status header badge and overall module
 * progress.
 *
 * Grouping: lectures and rich exams both carry `section_id` → the
 * translatable `course_sections.name` (confirmed the real grouping field —
 * see LectureProgressService::getLectureProgress). Rich assignments have no
 * `section_id` of their own (only a session-level cohort_scope pivot, a
 * different concept), so they're placed in a trailing "Assessments" group
 * rather than guessing a week association the schema doesn't support.
 *
 * Reuses LectureProgressService::getLectureProgress and
 * CertificateProjectionService::projectForCourse rather than re-deriving
 * either — no forked business logic.
 */
class LearnerCoursePlayerService
{
    public function __construct(
        private readonly LectureProgressService $progress,
        private readonly CertificateProjectionService $certificates,
    ) {}

    public function outline(User $user, Course $course): array
    {
        $locale = app()->getLocale();

        $lectures = collect($this->progress->getLectureProgress($user->id, $course->id));

        $exams = CourseExam::where('course_id', $course->id)
            ->get()
            ->filter(fn (CourseExam $exam) => $this->isRichExam($exam));

        $assignments = CourseAssignment::where('course_id', $course->id)
            ->get()
            ->filter(fn (CourseAssignment $assignment) => $this->isRichAssignment($assignment));

        $userExamsByExamId = UserExam::where('user_id', $user->id)
            ->whereIn('exam_id', $exams->pluck('id'))
            ->get()
            ->keyBy('exam_id');

        $userAssignmentsByAssignmentId = UserCourseAssignment::where('user_id', $user->id)
            ->whereIn('course_assignment_id', $assignments->pluck('id'))
            ->get()
            ->keyBy('course_assignment_id');

        // Group into "Week N" buckets by the module's assigned session number
        // (Figma 913-*). Modules without a session number fall back to their
        // module/section name, then a trailing "General content" bucket.
        // $order keeps weeks numerically sorted ahead of the assessments tail.
        $groups = [];
        $order  = [];

        foreach ($lectures as $lecture) {
            $n = $lecture['session_number'] ?? null;
            if ($n) {
                $label = __('messages.course_player.week', ['number' => $n]);
                $sort  = $n;
            } else {
                $label = $lecture['module']['name'] ?? __('messages.course_player.general_content');
                $sort  = 9000;
            }
            $groups[$label][] = [
                'kind' => 'lecture',
                'id' => $lecture['lecture_id'],
                'title' => $lecture['title'],
                'content_type' => $lecture['content_type'],
                'completed' => $lecture['completed'],
            ];
            $order[$label] ??= $sort;
        }

        foreach ($exams as $exam) {
            $label = $exam->section_id
                ? ($this->sectionName($exam->section_id, $locale) ?? __('messages.course_player.assessments_group'))
                : __('messages.course_player.assessments_group');

            $userExam = $userExamsByExamId->get($exam->id);

            $groups[$label][] = [
                'kind' => 'quiz',
                'id' => $exam->id,
                'title' => $exam->getTranslation('title', $locale),
                'content_type' => null,
                'completed' => $userExam?->submission_status === UserExam::SUBMISSION_SUBMITTED,
            ];
            $order[$label] ??= 9500;
        }

        foreach ($assignments as $assignment) {
            $label = __('messages.course_player.assessments_group');
            $userAssignment = $userAssignmentsByAssignmentId->get($assignment->id);

            $groups[$label][] = [
                'kind' => 'assignment',
                'id' => $assignment->id,
                'title' => $assignment->title,
                'content_type' => null,
                'completed' => $userAssignment?->submitted_at !== null,
            ];
            $order[$label] ??= 9999;
        }

        uksort($groups, fn ($a, $b) => ($order[$a] ?? 9999) <=> ($order[$b] ?? 9999));

        $weeks = $this->flagActiveItem($groups);

        $modulesTotal = $lectures->count();
        $modulesCompleted = $lectures->filter(fn ($l) => $l['completed'])->count();

        return [
            'course_id' => $course->id,
            'course_title' => $course->getTranslation('title', $locale),
            'certificate_status' => $this->certificates->projectForCourse($user, $course),
            'modules_completed' => $modulesCompleted,
            'modules_total' => $modulesTotal,
            'weeks' => $weeks,
        ];
    }

    /**
     * Full content for a single lecture — the outline's per-item payload is
     * metadata-only (title/content_type/completed) for sidebar rendering, so
     * the content viewer fetches the actual body/URL for the selected item
     * here rather than bloating every outline response with every lecture's
     * full content up front.
     */
    public function lecture(User $user, Course $course, CourseLecture $lecture): array
    {
        $locale = app()->getLocale();

        $contentUrl = match ($lecture->content_type) {
            'video', 'document' => $lecture->type === 'file' && $lecture->video
                ? $lecture->getFileUrl($lecture->video)
                : $lecture->video,
            'link' => $lecture->video,
            default => null,
        };

        $progress = UserLectureProgress::where('user_id', $user->id)
            ->where('lecture_id', $lecture->id)
            ->first();

        return [
            'id' => $lecture->id,
            'title' => $lecture->getTranslation('title', $locale),
            'description' => $lecture->getTranslation('instructions', $locale),
            'content_type' => $lecture->content_type,
            'content_url' => $contentUrl ?: null,
            'body' => $lecture->content_type === 'article' ? $lecture->content : null,
            'duration_minutes' => $lecture->duration_minutes,
            'require_completion' => (bool) $lecture->require_completion,
            'completed' => (bool) ($progress?->completed ?? false),
        ];
    }

    /** A quiz only qualifies for the rich learner flow once it has rich-authored questions. */
    private function isRichExam(CourseExam $exam): bool
    {
        return CourseExamQuestion::where('course_exam_id', $exam->id)->whereNotNull('question_en')->exists();
    }

    private function isRichAssignment(CourseAssignment $assignment): bool
    {
        return CourseAssignmentQuestion::where('course_assignment_id', $assignment->id)->exists();
    }

    private function sectionName(int $sectionId, string $locale): ?string
    {
        static $cache = [];

        if (! array_key_exists($sectionId, $cache)) {
            $section = CourseSection::find($sectionId);
            $cache[$sectionId] = $section?->getTranslation('name', $locale);
        }

        return $cache[$sectionId];
    }

    /**
     * Marks the first incomplete item across the whole flattened playlist as
     * `active` — the sidebar's current-position indicator and the
     * dashboard's "Continue Learning" resume target.
     *
     * @param  array<string, array<int, array<string, mixed>>>  $groups
     * @return array<int, array{label: string, items: array}>
     */
    private function flagActiveItem(array $groups): array
    {
        $activeAssigned = false;

        $weeks = collect($groups)->map(function (array $items, string $label) use (&$activeAssigned) {
            $items = array_map(function (array $item) use (&$activeAssigned) {
                $isActive = ! $activeAssigned && ! $item['completed'];
                if ($isActive) {
                    $activeAssigned = true;
                }
                $item['active'] = $isActive;

                return $item;
            }, $items);

            return ['label' => $label, 'items' => $items];
        })->values();

        return $weeks->all();
    }
}
