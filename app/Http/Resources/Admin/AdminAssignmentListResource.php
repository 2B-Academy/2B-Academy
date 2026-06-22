<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Lightweight list-view resource matching the Figma "Created Assignments"
 * mini-table: title, course, cohort scope (with pill), question count,
 * total score, due date, status.
 */
class AdminAssignmentListResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $cohorts = $this->whenLoaded('cohorts', fn () => $this->cohorts->map(fn ($c) => [
            'id'    => $c->course_session_id,
            'title' => optional($c->session)->title,
        ])->values(), collect());

        return [
            'id'              => $this->id,
            'title'           => $this->title,
            'title_ar'        => $this->title_ar,
            'course_id'       => $this->course_id,
            'course_title'    => $this->whenLoaded('course', fn () => $this->course->title),
            'cohort_scope'    => $this->cohort_scope,
            'cohorts'         => $cohorts,
            'questions_count' => (int) ($this->questions_count ?? 0),
            'total_score'     => (int) $this->total_score,
            'pass_score'      => $this->pass_score !== null ? (int) $this->pass_score : null,
            'status'          => $this->status,
            'due_date'        => $this->due_date?->format('Y-m-d'),
            'created_at'      => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
