<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class CourseAssignmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'course_id'  => $this->course_id,
            'title'      => $this->title,
            'file_url'   => $this->file ? url(Storage::disk('public')->url($this->file)) : null,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
