<?php

declare(strict_types=1);

namespace App\Http\Resources\Mobile;

use App\Enums\Mobile\RatingSentiment;
use App\Models\Course;
use App\Services\Mobile\MobileSettings;
use App\Services\Mobile\MyLearningService;
use App\Services\Mobile\QualificationProgressService;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * S-05 active course card. Includes:
 *
 *   - the enrolled cohort snapshot (resolved via the usersCourses
 *     pivot loaded by the repository)
 *   - live progress numbers (lectures completed, attendance, absences)
 *   - "Next: …" hint pulled from the next-unfinished lecture
 *   - whether a live session is happening right now (Figma "Live Now"
 *     badge on the active course card)
 */
class MyLearningActiveCourseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var Course $course */
        $course   = $this->resource;
        $locale   = app()->getLocale();
        $myLearn  = app(MyLearningService::class);
        $authUser = $request->user();

        // Cohort comes from the eager-loaded pivot row.
        $pivot  = $course->usersCourses->first();
        $cohort = $pivot?->group;

        $progress = $cohort
            ? $myLearn->progressFor($authUser, $course, (int) $cohort->id, $locale)
            : [
                'attended' => 0, 'past_sessions' => 0, 'total_sessions' => 0,
                'absences' => 0, 'progress_percent' => 0,
                'completed_lectures' => 0, 'total_lectures' => 0,
                'next_unit_title' => null,
            ];

        $liveSession = $cohort
            ? $myLearn->liveSessionFor($authUser, $course->id, (int) $cohort->id, now())
            : null;

        // Up-next session ("Session 3" / "الجلسة 3") — the first session
        // whose turn comes after the current/finished one.
        $nextSession = $cohort
            ? $myLearn->nextSessionFor($authUser, $course->id, (int) $cohort->id, $locale)
            : null;

        // The learner's own rating for this course (null ⇒ app shows the
        // "Add rate" CTA, otherwise it renders the existing score).
        $userRating = $myLearn->userRatingForCourse($authUser, $course->id);

        // Resolve the numeric rating to a localized sentiment label
        // ("Very satisfied" / "راضي جدًا"). Null when not rated yet.
        $rateValue = $userRating !== null ? (int) $userRating->rating : null;
        $rateLabel = null;
        if ($rateValue !== null) {
            $settings  = app(MobileSettings::class);
            $rateLabel = __(RatingSentiment::fromRating(
                $rateValue,
                $settings->ratingMinValue(),
                $settings->ratingMaxValue(),
            )->labelKey());
        }

        // Whether the learner has already marked attendance for the
        // currently-live session — lets the app hide the "Mark Present"
        // button once attendance is recorded.
        $liveSessionAttended = $liveSession !== null
            && $authUser !== null
            && DB::table('attendances')
                ->where('user_id', $authUser->id)
                ->where('session_id', (int) $liveSession->id)
                ->exists();

        return [
            'id'             => (int) $course->id,
            'title'          => $course->getTranslation('title', $locale),
            'course_type'    => $course->course_type,
            'image'          => $this->absoluteUrl($course->image),
            'hours'          => (int) ($course->hours ?? 0),
            'category'       => $course->relationLoaded('category') && $course->category
                ? [
                    'id'   => (int) $course->category->id,
                    'name' => $course->category->getTranslation('name', $locale),
                ]
                : null,
            'instructors'    => $course->relationLoaded('instructors')
                ? $course->instructors->map(fn ($i) => [
                    'id'    => (int) $i->id,
                    'name'  => (string) $i->name,
                    'image' => $this->absoluteUrl($i->image),
                ])->values()
                : [],
            'cohort'         => $cohort ? [
                'id'         => (int) $cohort->id,
                'name'       => $cohort->getTranslation('name', $locale),
                'start_date' => $cohort->start_date instanceof \Carbon\Carbon
                    ? $cohort->start_date->format('Y-m-d')
                    : $cohort->start_date,
                'end_date'   => $cohort->end_date instanceof \Carbon\Carbon
                    ? $cohort->end_date->format('Y-m-d')
                    : $cohort->end_date,
            ] : null,
            'progress'       => [
                'percent'            => (int) $progress['progress_percent'],
                'completed_lectures' => (int) $progress['completed_lectures'],
                'total_lectures'     => (int) $progress['total_lectures'],
                'attended_sessions'  => (int) $progress['attended'],
                'past_sessions'      => (int) $progress['past_sessions'],
                'total_sessions'     => (int) $progress['total_sessions'],
                'absences'           => (int) $progress['absences'],
                'next_unit_title'    => $progress['next_unit_title'],
            ],
            // The learner's own rating (null when not rated yet).
            'rate'           => $rateValue,
            // Localized sentiment label for the rating (null when unrated).
            'rate_label'     => $rateLabel,
            // Next session up (after the current one finishes).
            'session_number' => $nextSession['number'] ?? null,
            'session_name'   => $nextSession['name'] ?? null,
            // True when a session of the enrolled cohort is live right now
            // (drives the Figma "Live Now" badge). Mirrors live_session != null.
            'isLive'         => $liveSession !== null,
            'live_session'   => $liveSession ? [
                'id'           => (int) $liveSession->id,
                'title'        => $liveSession->title,
                'session_date' => $liveSession->session_date,
                'time_from'    => $liveSession->time_from,
                'time_to'      => $liveSession->time_to,
                'location'     => $liveSession->location,
                // True once the learner has marked present for this
                // session — the app uses this to hide the Mark Present CTA.
                'attended'     => $liveSessionAttended,
            ] : null,
            // Audit identity for cross-reference with HR — the machine_code
            // is the same value that gets denormalized into attendances when
            // the learner marks present from this card.
            'learner_machine_code' => $authUser?->machine_code,
        ];
    }

    private function absoluteUrl(?string $path): ?string
    {
        if ($path === null || $path === '') return null;
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }
        return asset('storage/' . ltrim($path, '/'));
    }
}
