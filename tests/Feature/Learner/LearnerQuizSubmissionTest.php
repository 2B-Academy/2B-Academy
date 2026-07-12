<?php

namespace Tests\Feature\Learner;

use App\Models\Course;
use App\Models\CourseExam;
use App\Models\CourseExamQuestion;
use App\Models\User;
use App\Services\Admin\AdminQuizService;
use Tests\Feature\Api\ApiTestCase;

/**
 * End-to-end coverage for the learner-facing rich Quiz submission flow:
 * resume-after-disconnect, per-question-type grading, auto-finalize, and
 * live results (including after an instructor grades a pending open answer).
 */
class LearnerQuizSubmissionTest extends ApiTestCase
{
    private function buildQuiz(Course $course): array
    {
        $quiz = CourseExam::factory()->create([
            'course_id' => $course->id,
            'title' => json_encode(['en' => 'Rich Quiz', 'ar' => 'اختبار غني']),
            'pass_score' => 15,
            'total_score' => 40,
            'status' => 'active',
        ]);

        $mcq = CourseExamQuestion::create([
            'course_exam_id' => $quiz->id,
            'position' => 0,
            'type' => 'mcq',
            'score' => 10,
            'question' => json_encode(['en' => 'legacy mirror', 'ar' => 'legacy mirror']),
            'question_en' => 'What is 2 + 2?',
            'question_ar' => 'كم يساوي 2 + 2؟',
            'options_en' => ['3', '4', '5'],
            'options_ar' => ['٣', '٤', '٥'],
            'correct_answer_en' => '4',
            'correct_answer_ar' => '٤',
        ]);

        $yesNo = CourseExamQuestion::create([
            'course_exam_id' => $quiz->id,
            'position' => 1,
            'type' => 'yes_no',
            'score' => 5,
            'question' => json_encode(['en' => 'legacy', 'ar' => 'legacy']),
            'question_en' => 'Is water wet?',
            'correct_answer_en' => 'yes',
        ]);

        $open = CourseExamQuestion::create([
            'course_exam_id' => $quiz->id,
            'position' => 2,
            'type' => 'open',
            'score' => 10,
            'question' => json_encode(['en' => 'legacy', 'ar' => 'legacy']),
            'question_en' => 'Explain photosynthesis.',
        ]);

        $reorder = CourseExamQuestion::create([
            'course_exam_id' => $quiz->id,
            'position' => 3,
            'type' => 'reorder',
            'score' => 15,
            'question' => json_encode(['en' => 'legacy', 'ar' => 'legacy']),
            'question_en' => 'Order the steps.',
            'options_en' => ['Step 1', 'Step 2', 'Step 3'],
            'correct_answer_en' => json_encode(['Step 1', 'Step 2', 'Step 3']),
        ]);

        return compact('quiz', 'mcq', 'yesNo', 'open', 'reorder');
    }

    public function test_take_returns_ordered_questions_without_correct_answers(): void
    {
        $course = Course::factory()->create();
        ['quiz' => $quiz] = $this->buildQuiz($course);
        ['headers' => $headers] = $this->userToken();

        $response = $this->withHeaders($headers)->getJson(self::BASE . "/courses/{$course->id}/quizzes/{$quiz->id}/take");

        $this->assertSuccess($response);
        $result = $response->json('result');

        $this->assertSame(4, $result['quiz']['questions_count']);
        $this->assertSame(0, $result['quiz']['answered_count']);
        $this->assertCount(4, $result['questions']);
        $this->assertArrayNotHasKey('correct_answer_en', $result['questions'][0]);
        $this->assertArrayNotHasKey('correct_answer', $result['questions'][0]);
        $this->assertSame('What is 2 + 2?', $result['questions'][0]['question']);
        $this->assertSame(['3', '4', '5'], $result['questions'][0]['options']);
    }

    public function test_mcq_answer_grades_immediately(): void
    {
        $course = Course::factory()->create();
        ['quiz' => $quiz, 'mcq' => $mcq] = $this->buildQuiz($course);
        ['headers' => $headers] = $this->userToken();

        $response = $this->withHeaders($headers)->postJson(
            self::BASE . "/courses/{$course->id}/quizzes/{$quiz->id}/questions/{$mcq->id}/answer",
            ['value' => '4'],
        );

        $this->assertSuccess($response);
        $result = $response->json('result');

        $this->assertTrue($result['is_correct']);
        $this->assertSame(10, $result['awarded_score']);
        $this->assertFalse($result['pending']);
        $this->assertSame(10, $result['running_total_score']);
        $this->assertSame(1, $result['answered_count']);
        $this->assertFalse($result['finalized']);
    }

    public function test_open_answer_is_pending_and_contributes_zero(): void
    {
        $course = Course::factory()->create();
        ['quiz' => $quiz, 'open' => $open] = $this->buildQuiz($course);
        ['headers' => $headers] = $this->userToken();

        $response = $this->withHeaders($headers)->postJson(
            self::BASE . "/courses/{$course->id}/quizzes/{$quiz->id}/questions/{$open->id}/answer",
            ['value' => 'Plants convert light into energy.'],
        );

        $this->assertSuccess($response);
        $result = $response->json('result');

        $this->assertTrue($result['pending']);
        $this->assertNull($result['is_correct']);
        $this->assertNull($result['awarded_score']);
        $this->assertSame(0, $result['running_total_score']);
    }

    public function test_reorder_answer_grades_proportionally(): void
    {
        $course = Course::factory()->create();
        ['quiz' => $quiz, 'reorder' => $reorder] = $this->buildQuiz($course);
        ['headers' => $headers] = $this->userToken();

        // Only position 0 ("Step 1") matches the correct order => 1 * (15/3) = 5 points.
        $response = $this->withHeaders($headers)->postJson(
            self::BASE . "/courses/{$course->id}/quizzes/{$quiz->id}/questions/{$reorder->id}/answer",
            ['order' => ['Step 1', 'Step 3', 'Step 2']],
        );

        $this->assertSuccess($response);
        $result = $response->json('result');

        $this->assertFalse($result['is_correct']);
        $this->assertSame(5, $result['awarded_score']);
    }

    public function test_resume_preserves_saved_answers_and_reports_next_question(): void
    {
        $course = Course::factory()->create();
        ['quiz' => $quiz, 'mcq' => $mcq, 'yesNo' => $yesNo] = $this->buildQuiz($course);
        ['headers' => $headers] = $this->userToken();

        $this->withHeaders($headers)->postJson(
            self::BASE . "/courses/{$course->id}/quizzes/{$quiz->id}/questions/{$mcq->id}/answer",
            ['value' => '4'],
        )->assertOk();

        // Simulate a fresh "disconnect and come back" GET.
        $response = $this->withHeaders($headers)->getJson(self::BASE . "/courses/{$course->id}/quizzes/{$quiz->id}/take");

        $result = $response->json('result');
        $this->assertSame(1, $result['quiz']['answered_count']);
        $this->assertSame($yesNo->id, $result['resume_question_id']);
        $this->assertTrue($result['questions'][0]['is_answered']);
        $this->assertSame(['value' => '4'], $result['questions'][0]['my_answer']);
        $this->assertFalse($result['questions'][1]['is_answered']);
    }

    public function test_answering_the_last_question_auto_finalizes_the_attempt(): void
    {
        $course = Course::factory()->create();
        ['quiz' => $quiz, 'mcq' => $mcq, 'yesNo' => $yesNo, 'open' => $open, 'reorder' => $reorder] = $this->buildQuiz($course);
        ['headers' => $headers] = $this->userToken();

        $this->withHeaders($headers)->postJson(self::BASE . "/courses/{$course->id}/quizzes/{$quiz->id}/questions/{$mcq->id}/answer", ['value' => '4'])->assertOk();
        $this->withHeaders($headers)->postJson(self::BASE . "/courses/{$course->id}/quizzes/{$quiz->id}/questions/{$yesNo->id}/answer", ['value' => 'yes'])->assertOk();
        $this->withHeaders($headers)->postJson(self::BASE . "/courses/{$course->id}/quizzes/{$quiz->id}/questions/{$open->id}/answer", ['value' => 'answer'])->assertOk();

        $response = $this->withHeaders($headers)->postJson(
            self::BASE . "/courses/{$course->id}/quizzes/{$quiz->id}/questions/{$reorder->id}/answer",
            ['order' => ['Step 1', 'Step 2', 'Step 3']],
        );

        $result = $response->json('result');
        $this->assertTrue($result['finalized']);
        $this->assertNotNull($result['results']);
        // 10 (mcq) + 5 (yes_no) + 0 (pending open) + 15 (reorder) = 30
        $this->assertSame(30, $result['results']['total_score']);
        $this->assertSame('submitted', $result['results']['submission_status']);
        $this->assertTrue($result['results']['passed']); // pass_score = 15

        // Results endpoint is independently re-fetchable afterwards.
        $resultsResponse = $this->withHeaders($headers)->getJson(self::BASE . "/courses/{$course->id}/quizzes/{$quiz->id}/results");
        $this->assertSuccess($resultsResponse);
        $this->assertSame(30, $resultsResponse->json('result.total_score'));

        $openBreakdown = collect($resultsResponse->json('result.answers'))->firstWhere('question_id', $open->id);
        $this->assertSame('pending', $openBreakdown['state']);
    }

    public function test_cannot_answer_after_the_quiz_has_been_submitted(): void
    {
        $course = Course::factory()->create();
        ['quiz' => $quiz, 'mcq' => $mcq, 'yesNo' => $yesNo, 'open' => $open, 'reorder' => $reorder] = $this->buildQuiz($course);
        ['headers' => $headers] = $this->userToken();

        foreach ([[$mcq, ['value' => '4']], [$yesNo, ['value' => 'yes']], [$open, ['value' => 'x']], [$reorder, ['order' => ['Step 1', 'Step 2', 'Step 3']]]] as [$question, $payload]) {
            $this->withHeaders($headers)->postJson(
                self::BASE . "/courses/{$course->id}/quizzes/{$quiz->id}/questions/{$question->id}/answer",
                $payload,
            )->assertOk();
        }

        $response = $this->withHeaders($headers)->postJson(
            self::BASE . "/courses/{$course->id}/quizzes/{$quiz->id}/questions/{$mcq->id}/answer",
            ['value' => '4'],
        );

        $response->assertStatus(409);
    }

    public function test_results_reflect_updated_score_after_instructor_grades_pending_open_answer(): void
    {
        $course = Course::factory()->create();
        ['quiz' => $quiz, 'mcq' => $mcq, 'yesNo' => $yesNo, 'open' => $open, 'reorder' => $reorder] = $this->buildQuiz($course);
        ['headers' => $headers] = $this->userToken();

        foreach ([[$mcq, ['value' => '4']], [$yesNo, ['value' => 'yes']], [$open, ['value' => 'x']], [$reorder, ['order' => ['Step 1', 'Step 2', 'Step 3']]]] as [$question, $payload]) {
            $this->withHeaders($headers)->postJson(
                self::BASE . "/courses/{$course->id}/quizzes/{$quiz->id}/questions/{$question->id}/answer",
                $payload,
            )->assertOk();
        }

        $submission = \App\Models\UserExam::where('exam_id', $quiz->id)->firstOrFail();
        $openAnswer = $submission->answers()->where('question_id', $open->id)->firstOrFail();

        app(AdminQuizService::class)->gradeAnswer($openAnswer, 10, 'Good answer', User::factory()->create());

        $response = $this->withHeaders($headers)->getJson(self::BASE . "/courses/{$course->id}/quizzes/{$quiz->id}/results");

        // 10 + 5 + 10 (now graded, full marks) + 15 = 40
        $this->assertSame(40, $response->json('result.total_score'));
        $openBreakdown = collect($response->json('result.answers'))->firstWhere('question_id', $open->id);
        $this->assertSame('correct', $openBreakdown['state']);
        $this->assertSame(10, $openBreakdown['awarded_score']);
    }
}
