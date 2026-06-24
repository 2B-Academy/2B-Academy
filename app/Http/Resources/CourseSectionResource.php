<?php

namespace App\Http\Resources;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseSectionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();

        // Roll the stored status forward to whatever the calendar
        // dictates now ("scheduled" rolls into "active" once start_date
        // arrives, "active" rolls into "completed" once end_date
        // passes). Manual `inactive` always wins so admins can still
        // park a cohort offline. The persisted column is left untouched
        // here — `php artisan cohorts:sync-statuses` is the source of
        // truth for the stored value, this resource just keeps reads
        // live in between cron runs.
        // Count sessions already held for this cohort. Prefer the
        // eager-loaded `held_sessions_count` (set by the repository) and
        // fall back to a fresh query for hand-built models.
        $heldSessions = $this->held_sessions_count !== null
            ? (int) $this->held_sessions_count
            : (int) $this->sessions()->ended()->count();

        $numberOfSessions = $this->number_of_sessions !== null
            ? (int) $this->number_of_sessions
            : null;

        $effectiveStatus = Course::deriveCohortStatus(
            $this->status,
            $this->start_date,
            $this->end_date,
            $numberOfSessions,
            $heldSessions,
        );

        return [
            'id'             => $this->id,
            'course_id'      => $this->course_id,
            // Localized display name for tables, plus the raw translations
            // map so the edit dialog can pre-fill both EN/AR inputs without
            // a follow-up request.
            'name'           => $this->getTranslation('name', $locale),
            'name_translations' => [
                'en' => $this->getTranslation('name', 'en'),
                'ar' => $this->getTranslation('name', 'ar'),
            ],
            // Cohort metadata (Figma 332:9988, 332:10708). All nullable.
            'start_date'     => $this->start_date?->format('Y-m-d'),
            'end_date'       => $this->end_date?->format('Y-m-d'),
            'capacity'       => $this->capacity !== null ? (int) $this->capacity : null,
            // Planned session count (Figma 332:10708). Shown in the cohort
            // table "Sessions" column and drives session-based completion.
            'number_of_sessions' => $numberOfSessions,
            'held_sessions_count' => $heldSessions,
            'status'         => $effectiveStatus,
            'stored_status'  => $this->status ?? 'scheduled',
            // Average session length in hours (drives the live attendance
            // window). Null = fall back to the global default.
            'avg_session_time' => $this->avg_session_time !== null
                ? (float) $this->avg_session_time
                : null,
            // Counted inline by the repository (`withCount`). Falls back
            // to a fresh sub-query if a caller hand-builds a section model.
            'enrolled_count' => (int) ($this->enrolled_count
                ?? $this->enrollments()->count()),

            'lectures' => $this->whenLoaded('lectures', fn () => CourseLectureResource::collection($this->lectures)),
            'exams'    => $this->whenLoaded('exams', fn () => CourseExamResource::collection($this->exams)),
        ];
    }
}
