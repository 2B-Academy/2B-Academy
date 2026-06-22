<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

/**
 * Paginated submissions row resource for the Figma "Submissions" table:
 * Learner, Course, Instructor, Assignment, Cohort, Submitted, Score, Status.
 *
 * The detail screen consumes additional question/answer data which is loaded
 * through `AdminAssignmentSubmissionDetailResource`.
 */
class AdminAssignmentSubmissionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $assignment   = $this->whenLoaded('assignment');
        $course       = $assignment ? optional($this->assignment->course) : null;
        $instructor   = $assignment ? optional($this->assignment->creator) : null;
        $cohortTitles = $assignment && $this->assignment->relationLoaded('cohorts')
            ? $this->assignment->cohorts->map(fn ($c) => optional($c->session)->title)->filter()->values()
            : collect();

        $max     = $this->max_score ?? 0;
        $awarded = $this->total_score;
        $percent = ($max > 0 && $awarded !== null) ? (int) round(($awarded / $max) * 100) : null;

        return [
            'id'               => $this->id,
            'assignment'       => $this->whenLoaded('assignment', fn () => [
                'id'        => $this->assignment->id,
                'title'     => $this->assignment->title,
                'course_id' => $this->assignment->course_id,
            ]),
            'assignment_title' => $assignment ? $this->assignment->title : null,
            'course_title'     => $course ? $course->title : null,
            'instructor_name'  => $instructor && isset($instructor->name) ? $instructor->name : null,
            'cohort_titles'    => $cohortTitles,
            'user'             => $this->whenLoaded('user', fn () => [
                'id'              => $this->user->id,
                'name'            => $this->user->name,
                'machine_code'    => $this->user->machine_code,
                'department_name' => $this->user->department_name,
            ]),
            'user_file_url'    => $this->user_file ? url(Storage::disk('public')->url($this->user_file)) : null,
            'total_score'      => $awarded !== null ? (int) $awarded : null,
            'max_score'        => (int) $max,
            'score_percent'    => $percent,
            'feedback'         => $this->feedback,
            'status'           => $awarded !== null ? 'graded' : 'pending',
            'submitted_at'     => $this->submitted_at?->format('Y-m-d H:i:s'),
            'reviewed_at'      => $this->reviewed_at?->format('Y-m-d H:i:s'),
            'created_at'       => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
