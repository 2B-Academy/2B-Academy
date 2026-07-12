<?php

namespace Tests\Unit\Grading;

use App\Services\Grading\QuestionAnswerGrader;
use PHPUnit\Framework\TestCase;

/**
 * Pure unit tests for the shared per-question-type grading rules used by
 * both the learner Quiz and Assignment submission flows.
 */
class QuestionAnswerGraderTest extends TestCase
{
    private function grader(): QuestionAnswerGrader
    {
        return new QuestionAnswerGrader();
    }

    private function question(array $overrides = []): object
    {
        return (object) array_merge([
            'type' => 'mcq',
            'score' => 10,
            'options_en' => ['A', 'B', 'C'],
            'options_ar' => ['أ', 'ب', 'ج'],
            'correct_answer_en' => 'B',
            'correct_answer_ar' => 'ب',
        ], $overrides);
    }

    public function test_mcq_correct_answer_awards_full_score(): void
    {
        $result = $this->grader()->grade($this->question(), ['value' => 'B']);

        $this->assertTrue($result['is_correct']);
        $this->assertSame(10, $result['awarded_score']);
        $this->assertFalse($result['pending']);
    }

    public function test_mcq_is_case_insensitive_and_trims_whitespace(): void
    {
        $result = $this->grader()->grade($this->question(), ['value' => '  b  ']);

        $this->assertTrue($result['is_correct']);
        $this->assertSame(10, $result['awarded_score']);
    }

    public function test_mcq_accepts_the_arabic_localized_answer_too(): void
    {
        $result = $this->grader()->grade($this->question(), ['value' => 'ب']);

        $this->assertTrue($result['is_correct']);
        $this->assertSame(10, $result['awarded_score']);
    }

    public function test_mcq_wrong_answer_awards_zero(): void
    {
        $result = $this->grader()->grade($this->question(), ['value' => 'A']);

        $this->assertFalse($result['is_correct']);
        $this->assertSame(0, $result['awarded_score']);
    }

    public function test_yes_no_grades_like_mcq(): void
    {
        $question = $this->question([
            'type' => 'yes_no',
            'options_en' => null,
            'options_ar' => null,
            'correct_answer_en' => 'yes',
            'correct_answer_ar' => 'نعم',
        ]);

        $this->assertTrue($this->grader()->grade($question, ['value' => 'yes'])['is_correct']);
        $this->assertFalse($this->grader()->grade($question, ['value' => 'no'])['is_correct']);
    }

    public function test_open_question_is_always_pending_with_null_score(): void
    {
        $question = $this->question(['type' => 'open', 'score' => 20]);

        $result = $this->grader()->grade($question, ['value' => 'My free-text answer.']);

        $this->assertNull($result['awarded_score']);
        $this->assertNull($result['is_correct']);
        $this->assertTrue($result['pending']);
    }

    public function test_reorder_awards_proportional_credit_per_correct_position(): void
    {
        // 20-point question, 5 items => 4 points per correctly-placed item.
        $question = $this->question([
            'type' => 'reorder',
            'score' => 20,
            'options_en' => ['Step 1', 'Step 2', 'Step 3', 'Step 4', 'Step 5'],
            'correct_answer_en' => json_encode(['Step 1', 'Step 2', 'Step 3', 'Step 4', 'Step 5']),
            'correct_answer_ar' => null,
        ]);

        // 3 of 5 positions correct (index 0, 1, 4).
        $submitted = ['Step 1', 'Step 2', 'Step 4', 'Step 3', 'Step 5'];

        $result = $this->grader()->grade($question, ['order' => $submitted]);

        $this->assertSame(12, $result['awarded_score']); // 3 * 4
        $this->assertFalse($result['is_correct']);
    }

    public function test_reorder_fully_correct_sequence_awards_full_score_and_is_correct(): void
    {
        $question = $this->question([
            'type' => 'reorder',
            'score' => 20,
            'correct_answer_en' => json_encode(['Step 1', 'Step 2', 'Step 3', 'Step 4', 'Step 5']),
        ]);

        $result = $this->grader()->grade($question, [
            'order' => ['Step 1', 'Step 2', 'Step 3', 'Step 4', 'Step 5'],
        ]);

        $this->assertSame(20, $result['awarded_score']);
        $this->assertTrue($result['is_correct']);
    }

    public function test_reorder_decodes_comma_separated_correct_answer(): void
    {
        $question = $this->question([
            'type' => 'reorder',
            'score' => 9,
            'correct_answer_en' => 'One, Two, Three',
        ]);

        $result = $this->grader()->grade($question, ['order' => ['One', 'Two', 'Three']]);

        $this->assertSame(9, $result['awarded_score']);
        $this->assertTrue($result['is_correct']);
    }

    public function test_correct_answer_for_display_returns_localized_order_for_reorder(): void
    {
        $question = $this->question([
            'type' => 'reorder',
            'correct_answer_en' => json_encode(['A', 'B', 'C']),
            'correct_answer_ar' => null,
        ]);

        $display = $this->grader()->correctAnswerForDisplay($question, 'en');

        $this->assertSame(['a', 'b', 'c'], $display);
    }

    public function test_correct_answer_for_display_falls_back_across_locales(): void
    {
        $question = $this->question(['correct_answer_ar' => null]);

        $this->assertSame('B', $this->grader()->correctAnswerForDisplay($question, 'ar'));
    }
}
