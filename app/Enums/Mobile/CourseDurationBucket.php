<?php

declare(strict_types=1);

namespace App\Enums\Mobile;

/**
 * Buckets a course's cohort calendar span (in weeks) for the learner-web
 * Catalogue filter sidebar (Figma: "1-2 Weeks / 2-4 Weeks / 4-8 Weeks /
 * +8 Weeks") and the course-detail "X weeks" stat.
 *
 * `duration_weeks` is derived from a cohort's `start_date`/`end_date`
 * span — `CEIL((end_date - start_date + 1) / 7)` — there is no stored
 * "duration in weeks" concept anywhere on `courses` or `course_sections`,
 * and `courses.hours` is total *instructional* hours, not a calendar
 * span, so it can't stand in for this.
 *
 * The bucket RANGES below are the single source of truth for the
 * boundaries: both `AcademyRepository` (SQL `HAVING`, for the list
 * filter + facet counts) and the card/detail resources (PHP, for the
 * displayed `duration_weeks` → bucket mapping used in tests/derivations)
 * read the ranges from here rather than re-hardcoding the 2/4/8 cutoffs.
 * Ranges are a non-overlapping partition of the weeks axis so a course
 * lands in exactly one bucket — a boundary week (2, 4, 8) belongs to the
 * SHORTER bucket.
 */
enum CourseDurationBucket: string
{
    case OneToTwoWeeks    = '1_2_weeks';
    case TwoToFourWeeks   = '2_4_weeks';
    case FourToEightWeeks = '4_8_weeks';
    case EightPlusWeeks   = '8_plus_weeks';

    /**
     * Inclusive `[min, max]` week range for this bucket. `max === null`
     * means unbounded above.
     *
     * @return array{0: int, 1: int|null}
     */
    public function range(): array
    {
        return match ($this) {
            self::OneToTwoWeeks    => [1, 2],
            self::TwoToFourWeeks   => [3, 4],
            self::FourToEightWeeks => [5, 8],
            self::EightPlusWeeks   => [9, null],
        };
    }

    /**
     * Classify a computed week count into its bucket. Returns `null` for
     * an unknown/zero duration (no dated cohort to derive it from).
     */
    public static function fromWeeks(?int $weeks): ?self
    {
        if ($weeks === null || $weeks < 1) {
            return null;
        }

        foreach (self::cases() as $bucket) {
            [$min, $max] = $bucket->range();
            if ($weeks >= $min && ($max === null || $weeks <= $max)) {
                return $bucket;
            }
        }

        return null;
    }

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_map(fn (self $case) => $case->value, self::cases());
    }
}
