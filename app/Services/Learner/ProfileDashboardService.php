<?php

declare(strict_types=1);

namespace App\Services\Learner;

use App\Models\Course;
use App\Models\User;
use App\Services\Mobile\QualificationProgressService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Learner-web Profile dashboard projections.
 *
 * The mobile /my-learning/qualifications endpoint only returns per-qualification
 * progress *counts*. The browser Profile screen additionally renders, inside
 * each qualification, the individual courses split into "earned" vs. not-yet
 * ("uncovered"), with certificate + cohort-availability + cross-qualification
 * context. It also shows four header counters (required / earned / in-progress /
 * not-started) that are course-level, not qualification-level.
 *
 * This service composes both, reusing {@see QualificationProgressService} as the
 * single source of truth for "what counts as a completed course" rather than
 * re-implementing that three-source rule.
 */
final class ProfileDashboardService
{
    public function __construct(
        private readonly QualificationProgressService $qualificationProgress,
    ) {}

    /**
     * Header counters. `required` = distinct courses the user's role requires;
     * `earned` = those completed; `in_progress` = those enrolled but not yet
     * completed; `not_started` = the remainder. The three sub-counts always
     * sum to `required`.
     *
     * @return array{required:int, earned:int, in_progress:int, not_started:int}
     */
    public function summaryCounts(User $user): array
    {
        $requiredCourseIds = $this->requiredCourseIds($user);
        if ($requiredCourseIds->isEmpty()) {
            return ['required' => 0, 'earned' => 0, 'in_progress' => 0, 'not_started' => 0];
        }

        $completed = $this->qualificationProgress
            ->completedCourseIdsForUser((int) $user->id)
            ->intersect($requiredCourseIds);

        $enrolled = DB::table('users_courses')
            ->where('user_id', $user->id)
            ->whereIn('course_id', $requiredCourseIds->all())
            ->pluck('course_id')
            ->map(fn ($v) => (int) $v);

        $required   = $requiredCourseIds->count();
        $earned     = $completed->count();
        $inProgress = $enrolled->diff($completed)->unique()->count();

        return [
            'required'    => $required,
            'earned'      => $earned,
            'in_progress' => $inProgress,
            'not_started' => max(0, $required - $earned - $inProgress),
        ];
    }

    /**
     * Per-qualification breakdown: each course split into earned vs uncovered,
     * with certificate id, completion date, cohort availability, and the other
     * required qualifications the course also counts toward ("also in").
     *
     * @return Collection<int, array<string, mixed>>
     */
    public function qualifications(User $user, string $locale): Collection
    {
        $qualifications = $this->requiredQualifications($user);
        if ($qualifications->isEmpty()) {
            return collect();
        }

        $qualificationIds = $qualifications->pluck('id')->map(fn ($v) => (int) $v)->all();

        // course_id => [qualification_id, ...] restricted to the user's required set.
        $courseToQualifications = DB::table('course_qualification_skills')
            ->whereIn('qualification_skill_id', $qualificationIds)
            ->get(['course_id', 'qualification_skill_id'])
            ->groupBy('course_id')
            ->map(fn ($rows) => $rows->pluck('qualification_skill_id')->map(fn ($v) => (int) $v)->all());

        $allCourseIds = collect($courseToQualifications->keys())->map(fn ($v) => (int) $v)->values();

        $completedIds   = $this->qualificationProgress->completedCourseIdsForUser((int) $user->id);
        $titles         = $this->courseTitles($allCourseIds, $locale);
        $qualNames      = $this->qualificationNames($qualifications, $locale);
        $certificates   = $this->certificatesByCourse($user, $allCourseIds);
        $completionDate  = $this->completionDatesByCourse($user, $allCourseIds);
        $scheduledCohort = $this->coursesWithScheduledCohort($allCourseIds);

        return $qualifications->map(function ($q) use (
            $courseToQualifications, $completedIds, $titles, $qualNames,
            $certificates, $completionDate, $scheduledCohort
        ) {
            $qid = (int) $q->id;

            $courseIds = $courseToQualifications
                ->filter(fn (array $quals) => in_array($qid, $quals, true))
                ->keys()
                ->map(fn ($v) => (int) $v)
                ->values();

            $earned    = collect();
            $uncovered = collect();

            foreach ($courseIds as $cid) {
                $alsoIn = collect($courseToQualifications->get($cid, []))
                    ->reject(fn ($otherQid) => (int) $otherQid === $qid)
                    ->map(fn ($otherQid) => $qualNames[(int) $otherQid] ?? null)
                    ->filter()
                    ->values()
                    ->all();

                if ($completedIds->contains($cid)) {
                    $earned->push([
                        'course_id'      => $cid,
                        'title'          => $titles[$cid] ?? '',
                        'completed_at'   => $completionDate[$cid] ?? null,
                        'certificate_id' => $certificates[$cid] ?? null,
                        'also_in'        => $alsoIn,
                    ]);
                } else {
                    $uncovered->push([
                        'course_id'        => $cid,
                        'title'            => $titles[$cid] ?? '',
                        'cohort_scheduled' => $scheduledCohort->contains($cid),
                        'also_in'          => $alsoIn,
                    ]);
                }
            }

            $total   = $courseIds->count();
            $percent = $total > 0 ? (int) floor(($earned->count() * 100) / $total) : 0;

            return [
                'id'                => $qid,
                'name'              => $qualNames[$qid] ?? '',
                'total_courses'     => $total,
                'completed_courses' => $earned->count(),
                'percent'           => $percent,
                'earned_courses'    => $earned->values()->all(),
                'uncovered_courses' => $uncovered->values()->all(),
            ];
        })->values();
    }

    /**
     * Courses the learner has completed — the "Completed" My-Learnings sub-tab.
     * The mobile "active" list deliberately excludes these (its cohort is
     * ended), so this is a separate web projection: title/image + completion
     * date + best final-exam score + certificate status.
     *
     * @return Collection<int, array<string, mixed>>
     */
    public function completedCourses(User $user, string $locale): Collection
    {
        $completedIds = $this->qualificationProgress->completedCourseIdsForUser((int) $user->id);
        if ($completedIds->isEmpty()) {
            return collect();
        }

        $certificates   = $this->certificatesByCourse($user, $completedIds);
        $completionDate  = $this->completionDatesByCourse($user, $completedIds);
        $scores          = $this->finalExamScoresByCourse($user, $completedIds);

        return Course::whereIn('id', $completedIds->all())
            ->get(['id', 'title', 'image', 'course_type'])
            ->map(function (Course $c) use ($locale, $certificates, $completionDate, $scores) {
                $cid = (int) $c->id;

                return [
                    'course_id'          => $cid,
                    'title'              => (string) $c->getTranslation('title', $locale),
                    'image'              => $c->image ? $c->getFileUrl($c->image) : null,
                    'course_type'        => $c->course_type,
                    'completed_at'       => $completionDate[$cid] ?? null,
                    'score_percent'      => $scores[$cid] ?? null,
                    'certificate_id'     => $certificates[$cid] ?? null,
                    'certificate_earned' => isset($certificates[$cid]),
                ];
            })
            ->values();
    }

    /** @return array<int, int> course_id => best final-exam percent */
    private function finalExamScoresByCourse(User $user, Collection $courseIds): array
    {
        if ($courseIds->isEmpty()) {
            return [];
        }

        $rows = DB::table('user_exams')
            ->join('course_exams', 'course_exams.id', '=', 'user_exams.exam_id')
            ->where('user_exams.user_id', $user->id)
            ->where('course_exams.is_final', true)
            ->whereIn('user_exams.course_id', $courseIds->all())
            ->get(['user_exams.course_id', 'user_exams.user_degree', 'user_exams.total_score', 'user_exams.max_score']);

        $result = [];
        foreach ($rows as $row) {
            $cid = (int) $row->course_id;
            $percent = (int) $row->max_score > 0
                ? (int) round(((int) $row->total_score * 100) / (int) $row->max_score)
                : (int) round((float) $row->user_degree);
            // Keep the learner's best attempt.
            $result[$cid] = isset($result[$cid]) ? max($result[$cid], $percent) : $percent;
        }

        return $result;
    }

    /** @return Collection<int, object{id:int, name:string}> */
    private function requiredQualifications(User $user): Collection
    {
        if (empty($user->job_title_id)) {
            return collect();
        }

        return DB::table('qualification_skills as qs')
            ->join('job_title_qualification_skill as jtqs', 'jtqs.qualification_skill_id', '=', 'qs.id')
            ->where('jtqs.job_title_id', $user->job_title_id)
            ->orderBy('qs.id')
            ->get(['qs.id', 'qs.name']);
    }

    /** @return Collection<int, int> */
    private function requiredCourseIds(User $user): Collection
    {
        $quals = $this->requiredQualifications($user);
        if ($quals->isEmpty()) {
            return collect();
        }

        return DB::table('course_qualification_skills')
            ->whereIn('qualification_skill_id', $quals->pluck('id')->all())
            ->distinct()
            ->pluck('course_id')
            ->map(fn ($v) => (int) $v)
            ->values();
    }

    /** @return array<int, string> */
    private function qualificationNames(Collection $qualifications, string $locale): array
    {
        return $qualifications
            ->mapWithKeys(fn ($q) => [(int) $q->id => $this->localizeJson($q->name, $locale)])
            ->all();
    }

    /** @return array<int, string> */
    private function courseTitles(Collection $courseIds, string $locale): array
    {
        if ($courseIds->isEmpty()) {
            return [];
        }

        return Course::whereIn('id', $courseIds->all())
            ->get(['id', 'title'])
            ->mapWithKeys(fn (Course $c) => [(int) $c->id => (string) $c->getTranslation('title', $locale)])
            ->all();
    }

    /** @return array<int, int> course_id => certificate_id */
    private function certificatesByCourse(User $user, Collection $courseIds): array
    {
        if ($courseIds->isEmpty()) {
            return [];
        }

        return DB::table('user_certificates')
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->whereIn('course_id', $courseIds->all())
            ->get(['id', 'course_id'])
            ->mapWithKeys(fn ($r) => [(int) $r->course_id => (int) $r->id])
            ->all();
    }

    /** @return array<int, string|null> course_id => Y-m-d */
    private function completionDatesByCourse(User $user, Collection $courseIds): array
    {
        if ($courseIds->isEmpty()) {
            return [];
        }

        // Certificate issue date takes precedence, then the latest completed
        // lecture-progress timestamp (courses completed purely by watching).
        $byCert = DB::table('user_certificates')
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->whereIn('course_id', $courseIds->all())
            ->pluck('issued_at', 'course_id');

        $byProgress = DB::table('user_lecture_progress as ulp')
            ->join('course_lectures as cl', 'cl.id', '=', 'ulp.lecture_id')
            ->where('ulp.user_id', $user->id)
            ->where('ulp.completed', true)
            ->whereIn('cl.course_id', $courseIds->all())
            ->selectRaw('cl.course_id, MAX(ulp.updated_at) AS completed_at')
            ->groupBy('cl.course_id')
            ->pluck('completed_at', 'course_id');

        $result = [];
        foreach ($courseIds as $cid) {
            $date = $byCert[$cid] ?? $byProgress[$cid] ?? null;
            $result[$cid] = $date ? Carbon::parse($date)->toDateString() : null;
        }

        return $result;
    }

    /** @return Collection<int, int> */
    private function coursesWithScheduledCohort(Collection $courseIds): Collection
    {
        if ($courseIds->isEmpty()) {
            return collect();
        }

        return DB::table('course_sections')
            ->whereIn('course_id', $courseIds->all())
            ->whereIn('status', ['scheduled', 'open_for_enrollment', 'active'])
            ->where(function ($q) {
                $q->whereNull('end_date')->orWhereDate('end_date', '>=', now()->toDateString());
            })
            ->distinct()
            ->pluck('course_id')
            ->map(fn ($v) => (int) $v)
            ->values();
    }

    private function localizeJson(?string $json, string $locale): string
    {
        if ($json === null) {
            return '';
        }

        $decoded = json_decode($json, true);
        if (is_array($decoded)) {
            return (string) ($decoded[$locale] ?? $decoded['en'] ?? $decoded['ar'] ?? '');
        }

        return (string) $json;
    }
}
