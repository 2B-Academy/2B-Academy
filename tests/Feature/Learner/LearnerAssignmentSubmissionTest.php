<?php

namespace Tests\Feature\Learner;

use App\Models\Course;
use App\Models\CourseAssignment;
use App\Models\CourseAssignmentQuestion;
use App\Models\User;
use App\Services\Admin\AdminAssignmentService;
use Tests\Feature\Api\ApiTestCase;

/**
 * End-to-end coverage for the learner-facing rich Assignment submission
 * flow — mirrors LearnerQuizSubmissionTest, reusing the exact same
 * QuestionAnswerGrader, plus the file-based/question-based discriminator.
 */
class LearnerAssignmentSubmissionTest extends ApiTestCase
{
    private function buildAssignment(Course $course): array
    {
        // No CourseAssignmentFactory exists in this codebase — create directly.
        $assignment = CourseAssignment::create([
            'course_id' => $course->id,
            'title' => 'Rich Assignment',
            'file' => null,
            'pass_score' => 10,
            'total_score' => 20,
            'status' => 'active',
        ]);

        $mcq = CourseAssignmentQuestion::create([
            'course_assignment_id' => $assignment->id,
            'position' => 0,
            'type' => 'mcq',
            'score' => 10,
            'question_en' => 'Pick the even number.',
            'options_en' => ['1', '2', '3'],
            'correct_answer_en' => '2',
        ]);

        $open = CourseAssignmentQuestion::create([
            'course_assignment_id' => $assignment->id,
            'position' => 1,
            'type' => 'open',
            'score' => 10,
            'question_en' => 'Describe your approach.',
        ]);

        return compact('assignment', 'mcq', 'open');
    }

    public function test_file_based_assignment_rejects_the_question_flow(): void
    {
        $course = Course::factory()->create();
        $assignment = CourseAssignment::create(['course_id' => $course->id, 'title' => 'Plain file assignment', 'file' => 'assignments/instructions.pdf']); // no questions
        ['headers' => $headers] = $this->userToken();

        $response = $this->withHeaders($headers)->getJson(self::BASE . "/courses/{$course->id}/assignments/{$assignment->id}/take");

        $response->assertStatus(422);
    }

    public function test_take_returns_ordered_questions_without_correct_answers(): void
    {
        $course = Course::factory()->create();
        ['assignment' => $assignment] = $this->buildAssignment($course);
        ['headers' => $headers] = $this->userToken();

        $response = $this->withHeaders($headers)->getJson(self::BASE . "/courses/{$course->id}/assignments/{$assignment->id}/take");

        $this->assertSuccess($response);
        $result = $response->json('result');

        $this->assertSame(2, $result['assignment']['questions_count']);
        $this->assertCount(2, $result['questions']);
        $this->assertArrayNotHasKey('correct_answer_en', $result['questions'][0]);
    }

    public function test_mcq_answer_grades_immediately_and_open_is_pending(): void
    {
        $course = Course::factory()->create();
        ['assignment' => $assignment, 'mcq' => $mcq, 'open' => $open] = $this->buildAssignment($course);
        ['headers' => $headers] = $this->userToken();

        $mcqResponse = $this->withHeaders($headers)->postJson(
            self::BASE . "/courses/{$course->id}/assignments/{$assignment->id}/questions/{$mcq->id}/answer",
            ['value' => '2'],
        );
        $this->assertTrue($mcqResponse->json('result.is_correct'));
        $this->assertSame(10, $mcqResponse->json('result.awarded_score'));

        $openResponse = $this->withHeaders($headers)->postJson(
            self::BASE . "/courses/{$course->id}/assignments/{$assignment->id}/questions/{$open->id}/answer",
            ['value' => 'My detailed approach.'],
        );

        $this->assertTrue($openResponse->json('result.pending'));
        $this->assertNull($openResponse->json('result.awarded_score'));
        // Last question answered => auto-finalized. Pending open scores 0
        // until graded, so total is just the mcq's 10 points.
        $this->assertTrue($openResponse->json('result.finalized'));
        $this->assertSame(10, $openResponse->json('result.results.total_score'));
        $this->assertSame('submitted', $openResponse->json('result.results.submission_status'));
    }

    public function test_results_reflect_updated_score_after_instructor_grades_pending_open_answer(): void
    {
        $course = Course::factory()->create();
        ['assignment' => $assignment, 'mcq' => $mcq, 'open' => $open] = $this->buildAssignment($course);
        ['headers' => $headers] = $this->userToken();

        $this->withHeaders($headers)->postJson(self::BASE . "/courses/{$course->id}/assignments/{$assignment->id}/questions/{$mcq->id}/answer", ['value' => '2'])->assertOk();
        $this->withHeaders($headers)->postJson(self::BASE . "/courses/{$course->id}/assignments/{$assignment->id}/questions/{$open->id}/answer", ['value' => 'x'])->assertOk();

        $submission = \App\Models\UserCourseAssignment::where('course_assignment_id', $assignment->id)->firstOrFail();
        $openAnswer = $submission->answers()->where('course_assignment_question_id', $open->id)->firstOrFail();

        app(AdminAssignmentService::class)->gradeAnswer($openAnswer, 10, 'Great work', User::factory()->create());

        $response = $this->withHeaders($headers)->getJson(self::BASE . "/courses/{$course->id}/assignments/{$assignment->id}/results");

        $this->assertSame(20, $response->json('result.total_score'));
        $this->assertTrue($response->json('result.passed'));
        $openBreakdown = collect($response->json('result.answers'))->firstWhere('question_id', $open->id);
        $this->assertSame('correct', $openBreakdown['state']);
    }

    public function test_cannot_answer_after_assignment_already_submitted(): void
    {
        $course = Course::factory()->create();
        ['assignment' => $assignment, 'mcq' => $mcq, 'open' => $open] = $this->buildAssignment($course);
        ['headers' => $headers] = $this->userToken();

        $this->withHeaders($headers)->postJson(self::BASE . "/courses/{$course->id}/assignments/{$assignment->id}/questions/{$mcq->id}/answer", ['value' => '2'])->assertOk();
        $this->withHeaders($headers)->postJson(self::BASE . "/courses/{$course->id}/assignments/{$assignment->id}/questions/{$open->id}/answer", ['value' => 'x'])->assertOk();

        $response = $this->withHeaders($headers)->postJson(
            self::BASE . "/courses/{$course->id}/assignments/{$assignment->id}/questions/{$mcq->id}/answer",
            ['value' => '2'],
        );

        $response->assertStatus(409);
    }
}
