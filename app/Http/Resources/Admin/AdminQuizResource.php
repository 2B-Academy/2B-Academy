<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Full quiz payload for admin detail / create-edit screens.
 * Includes every question and the cohort scope. Use the lighter
 * list resource for paginated listings.
 */
class AdminQuizResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'course_id'       => $this->course_id,
            'course'          => $this->whenLoaded('course', fn () => $this->course ? [
                'id'    => $this->course->id,
                'title' => $this->course->title,
            ] : null),
            'title'           => $this->title,
            'title_ar'        => $this->title_ar,
            'instructions_en' => $this->instructions_en,
            'instructions_ar' => $this->instructions_ar,
            'due_date'        => $this->due_date?->format('Y-m-d'),
            'cohort_scope'    => $this->cohort_scope,
            'pass_score'      => $this->pass_score !== null ? (int) $this->pass_score : null,
            'total_score'     => (int) $this->total_score,
            'status'          => $this->status,
            'created_by'      => $this->created_by,
            'created_by_user' => $this->whenLoaded('creator', fn () => $this->creator ? [
                'id'   => $this->creator->id,
                'name' => $this->creator->name,
            ] : null),
            'cohorts'         => $this->whenLoaded('cohorts', fn () => $this->cohorts->map(fn ($c) => [
                'id'    => $c->course_session_id,
                'title' => optional($c->session)->title,
            ])->values()),
            'questions'       => AdminQuizQuestionResource::collection($this->whenLoaded('richQuestions')),
            'created_at'      => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at'      => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
