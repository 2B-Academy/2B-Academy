<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Paginated submissions row resource for the Figma quiz "Submissions" table:
 * Learner, Course, Instructor, Quiz, Cohort, Submitted, Score, Status.
 */
class AdminQuizSubmissionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $quiz         = $this->whenLoaded('exam');
        $course       = $quiz ? optional($this->exam->course) : null;
        $instructor   = $quiz ? optional($this->exam->creator) : null;
        $cohortTitles = $quiz && $this->exam->relationLoaded('cohorts')
            ? $this->exam->cohorts->map(fn ($c) => optional($c->session)->title)->filter()->values()
            : collect();

        $max     = $this->max_score ?? 0;
        $awarded = $this->total_score;
        $percent = ($max > 0 && $awarded !== null) ? (int) round(($awarded / $max) * 100) : null;

        return [
            'id'              => $this->id,
            'quiz'            => $this->whenLoaded('exam', fn () => [
                'id'        => $this->exam->id,
                'title'     => $this->exam->title,
                'course_id' => $this->exam->course_id,
            ]),
            'quiz_title'      => $quiz ? $this->exam->title : null,
            'course_title'    => $course ? $course->title : null,
            'instructor_name' => $instructor && isset($instructor->name) ? $instructor->name : null,
            'cohort_titles'   => $cohortTitles,
            'user'            => $this->whenLoaded('user', fn () => [
                'id'              => $this->user->id,
                'name'            => $this->user->name,
                'machine_code'    => $this->user->machine_code ?? null,
                'department_name' => $this->user->department_name ?? null,
            ]),
            'total_score'     => $awarded !== null ? (int) $awarded : null,
            'max_score'       => (int) $max,
            'score_percent'   => $percent,
            'attempts'        => (int) ($this->attempts_count ?? 1),
            'status'          => $awarded !== null ? 'graded' : 'pending',
            'submitted_at'    => $this->submitted_at?->format('Y-m-d H:i:s'),
            'reviewed_at'     => $this->reviewed_at?->format('Y-m-d H:i:s'),
            'created_at'      => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
