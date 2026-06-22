<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

/**
 * Full submission payload for the "View details" page — includes the
 * assignment, every question, and the learner's answer for each question.
 */
class AdminAssignmentSubmissionDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $assignment = $this->whenLoaded('assignment');
        $course     = $assignment ? optional($this->assignment->course) : null;
        $instructor = $assignment ? optional($this->assignment->creator) : null;

        $max     = $this->max_score ?? ($assignment ? (int) $this->assignment->total_score : 0);
        $awarded = $this->total_score;
        $percent = ($max > 0 && $awarded !== null) ? (int) round(($awarded / $max) * 100) : null;

        $answers = $this->whenLoaded('answers', fn () => $this->answers->map(function ($answer) {
            $q = $answer->question;
            return [
                'id'             => $answer->id,
                'awarded_score'  => $answer->awarded_score !== null ? (int) $answer->awarded_score : null,
                'is_correct'     => $answer->is_correct,
                'answer'         => $answer->answer,
                'feedback'       => $answer->feedback,
                'question'       => $q ? [
                    'id'                => $q->id,
                    'position'          => (int) $q->position,
                    'type'              => $q->type,
                    'score'             => (int) $q->score,
                    'question_en'       => $q->question_en,
                    'question_ar'       => $q->question_ar,
                    'options_en'        => $q->options_en ?? [],
                    'options_ar'        => $q->options_ar ?? [],
                    'correct_answer_en' => $q->correct_answer_en,
                    'correct_answer_ar' => $q->correct_answer_ar,
                    'explanation_en'    => $q->explanation_en,
                    'explanation_ar'    => $q->explanation_ar,
                ] : null,
            ];
        })->values());

        return [
            'id'               => $this->id,
            'assignment'       => $assignment ? [
                'id'           => $this->assignment->id,
                'title'        => $this->assignment->title,
                'title_ar'     => $this->assignment->title_ar,
                'status'       => $this->assignment->status,
                'cohort_scope' => $this->assignment->cohort_scope,
                'pass_score'   => $this->assignment->pass_score !== null ? (int) $this->assignment->pass_score : null,
                'total_score'  => (int) $this->assignment->total_score,
            ] : null,
            'course_title'     => $course ? $course->title : null,
            'instructor_name'  => $instructor && isset($instructor->name) ? $instructor->name : null,
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
            'answers'          => $answers,
        ];
    }
}
