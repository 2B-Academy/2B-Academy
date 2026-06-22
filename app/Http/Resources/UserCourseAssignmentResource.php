<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class UserCourseAssignmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'assignment'       => new CourseAssignmentResource($this->whenLoaded('assignment')),
            'assignment_title' => $this->whenLoaded('assignment', fn () => $this->assignment->title),
            'course_title'     => $this->whenLoaded('assignment', fn () => optional($this->assignment->course)->title),
            'user'             => $this->whenLoaded('user', fn () => [
                'id'              => $this->user->id,
                'name'            => $this->user->name,
                'machine_code'    => $this->user->machine_code,
                'department_name' => $this->user->department_name,
            ]),
            'user_file_url'    => $this->user_file
                ? url(Storage::disk('public')->url($this->user_file))
                : null,
            'feedback'         => $this->feedback,
            'score'            => $this->score,
            'status'           => $this->score !== null ? 'graded' : 'pending',
            'created_at'       => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
