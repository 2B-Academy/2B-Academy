<?php

namespace App\Services;

use App\Http\Traits\HasFile;
use App\Models\Course;
use App\Models\CourseAssignment;
use App\Models\User;
use App\Models\UserCourseAssignment;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class CourseAssignmentService
{
    use HasFile;

    public function listForCourse(Course $course): Collection
    {
        return $course->assignments()->orderBy('id')->get();
    }

    public function create(Course $course, string $title, UploadedFile $file): CourseAssignment
    {
        $path = $this->uploadRequestFile('CourseAssignment', request(), null, $file);
        return $course->assignments()->create(['title' => $title, 'file' => $path]);
    }

    public function update(CourseAssignment $assignment, string $title, ?UploadedFile $file): CourseAssignment
    {
        $data = ['title' => $title];
        if ($file) {
            $data['file'] = $this->uploadRequestFile('CourseAssignment', request(), null, $file);
        }
        $assignment->update($data);
        return $assignment->fresh();
    }

    public function delete(CourseAssignment $assignment): void
    {
        $assignment->delete();
    }

    public function listSubmissions(CourseAssignment $assignment, int $perPage = 20): LengthAwarePaginator
    {
        return UserCourseAssignment::with(['user:id,name,machine_code,department_name', 'assignment'])
            ->where('course_assignment_id', $assignment->id)
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    public function submitFile(CourseAssignment $assignment, User $user, UploadedFile $file): UserCourseAssignment
    {
        $path = $this->uploadRequestFile('UserAssignment', request(), null, $file);

        return UserCourseAssignment::updateOrCreate(
            ['user_id' => $user->id, 'course_assignment_id' => $assignment->id],
            ['user_file' => $path]
        );
    }

    public function reviewSubmission(UserCourseAssignment $submission, ?string $feedback, ?string $score): UserCourseAssignment
    {
        $submission->update(['feedback' => $feedback, 'score' => $score]);
        return $submission->fresh();
    }
}
