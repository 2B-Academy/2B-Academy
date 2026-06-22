<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttendanceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                   => $this->id,
            'user_id'              => $this->user_id,
            'user_machine_code'    => $this->user_machine_code,
            'user_department'      => $this->user_department,
            'course_id'            => $this->course_id,
            'course_name'          => $this->course_name,
            'course_hours'         => $this->course_hours,
            'course_category_id'   => $this->course_category_id,
            'course_category_name' => $this->course_category_name,
            'section_id'           => $this->section_id,
            'attendance_hours'     => $this->attendance_hours,
            'is_manual'            => (bool) $this->is_manual,
            'created_at'           => $this->created_at?->toDateTimeString(),
        ];
    }
}
