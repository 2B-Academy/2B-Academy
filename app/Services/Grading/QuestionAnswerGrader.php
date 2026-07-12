<?php

namespace App\Services\Grading;

/**
 * Single source of truth for per-question-type grading logic, shared by
 * the learner-facing rich Quiz submission flow (LearnerQuizService,
 * CourseExamQuestion) and the learner-facing rich Assignment submission
 * flow (LearnerAssignmentService, CourseAssignmentQuestion).
 *
 * Both question models expose the identical shape (type, score, options_en,
 * options_ar, correct_answer_en, correct_answer_ar) so this grader is typed
 * against a plain `object` rather than either concrete Eloquent model —
 * duck-typed on purpose so the exact same rule set backs both features and
 * is never forked/duplicated.
 *
 * Business rules (per NAS-LMS-Website spec):
 *   - mcq / yes_no : exact match (case-insensitive) against the configured
 *                    correct answer → full score or zero, graded immediately.
 *   - open         : never auto-graded. Always returned as "pending" with a
 *                    null awarded_score — contributes 0 to the running total
 *                    until an instructor grades it via the existing admin
 *                    grade endpoint (AdminQuizService::gradeAnswer /
 *                    AdminAssignmentService::gradeAnswer).
 *   - reorder      : proportional credit — (question score ÷ item count)
 *                    points per item placed in its correct position.
 */
class QuestionAnswerGrader
{
    public const TYPE_MCQ = 'mcq';
    public const TYPE_YES_NO = 'yes_no';
    public const TYPE_OPEN = 'open';
    public const TYPE_REORDER = 'reorder';

    /**
     * @param  object  $question  Any model exposing type/score/options_en/options_ar/correct_answer_en/correct_answer_ar.
     * @param  array<string, mixed>  $payload  `['value' => ...]` for mcq/yes_no/open, `['order' => string[]]` for reorder.
     * @return array{awarded_score: int|null, is_correct: bool|null, pending: bool}
     */
    public function grade(object $question, array $payload): array
    {
        return match ($question->type) {
            self::TYPE_MCQ, self::TYPE_YES_NO => $this->gradeChoice($question, $payload),
            self::TYPE_OPEN => $this->gradeOpen(),
            self::TYPE_REORDER => $this->gradeReorder($question, $payload),
            default => ['awarded_score' => 0, 'is_correct' => false, 'pending' => false],
        };
    }

    /**
     * The localized "model answer" to show the learner for comparison —
     * used by the results/answer response, not part of grading itself.
     */
    public function correctAnswerForDisplay(object $question, string $locale): mixed
    {
        if ($question->type === self::TYPE_REORDER) {
            $order = $this->decodeOrder($locale === 'ar' ? $question->correct_answer_ar : $question->correct_answer_en)
                ?? $this->decodeOrder($question->correct_answer_en)
                ?? $this->decodeOrder($question->correct_answer_ar);

            return $order ? array_values($order) : null;
        }

        return $locale === 'ar'
            ? ($question->correct_answer_ar ?? $question->correct_answer_en)
            : ($question->correct_answer_en ?? $question->correct_answer_ar);
    }

    private function gradeChoice(object $question, array $payload): array
    {
        $submitted = $this->normalize((string) ($payload['value'] ?? ''));

        $candidates = array_filter([
            $this->normalize((string) ($question->correct_answer_en ?? '')),
            $this->normalize((string) ($question->correct_answer_ar ?? '')),
        ], fn ($v) => $v !== '');

        $isCorrect = $submitted !== '' && in_array($submitted, $candidates, true);
        $score = (int) $question->score;

        return [
            'awarded_score' => $isCorrect ? $score : 0,
            'is_correct' => $isCorrect,
            'pending' => false,
        ];
    }

    private function gradeOpen(): array
    {
        return [
            'awarded_score' => null,
            'is_correct' => null,
            'pending' => true,
        ];
    }

    private function gradeReorder(object $question, array $payload): array
    {
        $submitted = array_map(
            fn ($v) => $this->normalize((string) $v),
            array_values((array) ($payload['order'] ?? []))
        );

        $correctOrder = $this->decodeOrder($question->correct_answer_en)
            ?? $this->decodeOrder($question->correct_answer_ar)
            ?? array_map(fn ($v) => $this->normalize((string) $v), (array) ($question->options_en ?? []));

        $itemCount = count($correctOrder);
        $score = (int) $question->score;

        if ($itemCount === 0) {
            return ['awarded_score' => 0, 'is_correct' => false, 'pending' => false];
        }

        $perItem = $score / $itemCount;
        $correctPositions = 0;

        foreach ($correctOrder as $index => $item) {
            if (($submitted[$index] ?? null) === $item) {
                $correctPositions++;
            }
        }

        $awarded = (int) round($perItem * $correctPositions);
        $awarded = max(0, min($awarded, $score));

        return [
            'awarded_score' => $awarded,
            'is_correct' => $correctPositions === $itemCount,
            'pending' => false,
        ];
    }

    private function normalize(string $value): string
    {
        return mb_strtolower(trim($value));
    }

    /**
     * `correct_answer_en/ar` for a reorder question stores the correct
     * sequence as either a JSON-encoded array string or a comma-separated
     * list (both are plausible admin-authoring encodings for a `text`
     * column) — decode defensively rather than assume one format.
     *
     * @return array<int, string>|null
     */
    private function decodeOrder(?string $raw): ?array
    {
        if ($raw === null || trim($raw) === '') {
            return null;
        }

        $decoded = json_decode($raw, true);
        if (is_array($decoded) && $decoded !== []) {
            return array_map(fn ($v) => $this->normalize((string) $v), $decoded);
        }

        if (str_contains($raw, ',')) {
            return array_map(fn ($v) => $this->normalize(trim($v)), explode(',', $raw));
        }

        return null;
    }
}
