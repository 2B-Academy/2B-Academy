<?php

namespace App\Repositories\Eloquents;

use App\Models\Course;
use App\Models\CourseExam;
use App\Models\CourseExamQuestion;
use App\Repositories\Contracts\CourseExamRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class CourseExamRepository extends BaseRepository implements CourseExamRepositoryInterface
{
    public function __construct()
    {
        parent::__construct(new CourseExam());
    }

    public function allForCourse(Course $course): Collection
    {
        return $course->exams()->with('questions.answers')->orderBy('id')->get();
    }

    public function findWithQuestions(int $id): CourseExam
    {
        return CourseExam::with('questions.answers')->findOrFail($id);
    }

    public function createWithQuestions(array $data): CourseExam
    {
        return DB::transaction(function () use ($data) {
            $exam = CourseExam::create([
                'course_id'  => $data['course_id'],
                'section_id' => $data['section_id'],
                'title'      => $data['title'],
                'degree'     => $data['degree'],
                'is_final'   => $data['is_final'] ?? false,
            ]);

            $this->syncQuestions($exam, $data['questions'] ?? []);

            return $exam->load('questions.answers');
        });
    }

    public function updateWithQuestions(CourseExam $exam, array $data): CourseExam
    {
        return DB::transaction(function () use ($exam, $data) {
            $exam->update([
                'section_id' => $data['section_id'] ?? $exam->section_id,
                'title'      => $data['title'] ?? $exam->title,
                'degree'     => $data['degree'] ?? $exam->degree,
                'is_final'   => $data['is_final'] ?? $exam->is_final,
            ]);

            foreach ($exam->questions as $q) {
                $q->answers()->delete();
                $q->delete();
            }

            $this->syncQuestions($exam, $data['questions'] ?? []);

            return $exam->fresh('questions.answers');
        });
    }

    private function syncQuestions(CourseExam $exam, array $questions): void
    {
        foreach ($questions as $questionData) {
            $question = CourseExamQuestion::create([
                'course_exam_id' => $exam->id,
                'question'       => $questionData['question'],
            ]);

            foreach ($questionData['answers'] as $index => $answerData) {
                if (!empty($answerData['answer'])) {
                    $question->answers()->create([
                        'answer'     => $answerData['answer'],
                        'is_correct' => (bool) ($answerData['is_correct'] ?? false),
                    ]);
                }
            }
        }
    }
}
