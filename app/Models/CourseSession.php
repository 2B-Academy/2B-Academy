<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseSession extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    /**
     * Sessions that have already taken place — their date is in the past,
     * or it's today and the end time has elapsed. Drives the "held
     * sessions" count that completes a cohort once it reaches the cohort's
     * configured `number_of_sessions` target.
     */
    public function scopeEnded($query)
    {
        $today   = now()->toDateString();
        $nowTime = now()->format('H:i:s');

        return $query->where(function ($q) use ($today, $nowTime) {
            $q->whereDate('session_date', '<', $today)
              ->orWhere(function ($q2) use ($today, $nowTime) {
                  $q2->whereDate('session_date', '=', $today)
                     ->whereNotNull('time_to')
                     ->where('time_to', '<=', $nowTime);
              });
        });
    }

    public function section()
    {
        return $this->belongsTo(CourseSection::class, 'section_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }
}
