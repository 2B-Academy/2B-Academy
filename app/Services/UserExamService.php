<?php

namespace App\Services;

use App\Models\Course;
use App\Models\CourseExam;
use App\Models\CourseExamQuestionAnswer;
use App\Models\User;
use App\Models\UserExam;
use Illuminate\Database\Eloquent\Collection;

class UserExamService
{
    public function __construct(
        private readonly CertificateService $certificates,
    ) {}

    public function hasAlreadySubmitted(int $userId, int $examId): bool
    {
        return UserExam::where('user_id', $userId)->where('exam_id', $examId)->exists();
    }

    /**
     * Submit exam answers, auto-grade, and return the stored UserExam.
     *
     * Expected $questions format:
     * [
     *   ['question_id' => 1, 'question_title' => '...', 'answer_id' => 3],
     *   ...
     * ]
     */
    public function submit(User $user, Course $course, CourseExam $exam, array $questions): UserExam
    {
        $userExam = UserExam::create([
            'user_id'   => $user->id,
            'course_id' => $course->id,
            'exam_id'   => $exam->id,
        ]);

        $correctAnswers = 0;

        foreach ($questions as $questionData) {
            $answer = CourseExamQuestionAnswer::find($questionData['answer_id'] ?? null)
                ?? new CourseExamQuestionAnswer();

            $userExam->answers()->create([
                'question_id' => $questionData['question_id'],
                'question'    => $questionData['question_title'],
                'answer_id'   => $answer->id ?? null,
                'answer'      => $answer->answer ?? null,
                'is_correct'  => (bool) ($answer->is_correct ?? false),
            ]);

            if ($answer->is_correct ?? false) {
                $correctAnswers++;
            }
        }

        $totalQuestions = count($questions);
        $userDegree     = $totalQuestions > 0
            ? ($exam->degree / $totalQuestions) * $correctAnswers
            : 0;

        $status = $userDegree >= ($exam->degree / 2) ? 'success' : 'fail';

        $userExam->update([
            'user_degree' => $userDegree,
            'status'      => $status,
        ]);

        // Issue the first-class certificate the moment a final exam is
        // passed on a certificate-bearing course. Idempotent + eligibility
        // gated inside CertificateService.
        if ($status === 'success') {
            $this->certificates->issueFromExam($userExam);
        }

        return $userExam->load(['exam:id,title,degree,is_final', 'course:id,title,certificate']);
    }

    public function getUserExams(int $userId): Collection
    {
        return UserExam::where('user_id', $userId)
            ->with(['course:id,title,certificate', 'exam:id,title,degree,is_final'])
            ->latest()
            ->get();
    }

    public function getUserExam(int $userId, int $examId): ?UserExam
    {
        return UserExam::where('user_id', $userId)
            ->where('id', $examId)
            ->with(['course:id,title,certificate', 'exam:id,title,degree,is_final', 'answers'])
            ->first();
    }
}
