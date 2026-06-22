<?php

declare(strict_types=1);

namespace App\Enums\Mobile;

/**
 * 5-point emoji sentiment scale used by the cohort feedback bottom
 * sheet (Figma 543:41637 / 543:41844).
 *
 * The enum is intentionally tied to the existing `course_ratings.rating`
 * column (int 1..5) — no schema change required. The mapping
 * 1 → "Unsatisfied at all" through 5 → "Very Satisfied" is the
 * canonical sentiment label set surfaced both on the rating dialog
 * and on the My Learning active course card / certificate row
 * (e.g. "Satisfied ★ 4.0").
 *
 * The whole scale range (min/max) and the negative-rating cutoff
 * that forces a comment are NOT baked here — they live in the
 * `settings` table (`rating_min_value`, `rating_max_value`,
 * `rating_comment_required_at_or_below`) and are read by
 * MobileSettings, so admins can re-shape the scale without a deploy.
 *
 * This enum is the *label resolver* for whatever scale the settings
 * yield: it answers "given a rating of N (clamped to [min, max]),
 * what sentiment do I show?".
 */
enum RatingSentiment: string
{
    case UnsatisfiedAtAll = 'unsatisfied_at_all';
    case Unsatisfied      = 'unsatisfied';
    case Neutral          = 'neutral';
    case Satisfied        = 'satisfied';
    case VerySatisfied    = 'very_satisfied';

    /**
     * Resolve the sentiment from a numeric rating value, given the
     * inclusive bounds of the scale (min..max). The scale is divided
     * into five buckets — first/last buckets always anchor the
     * extremes — so the mapping stays consistent whether admins
     * configure a 1..5, 1..10, or 0..4 scale via the settings table.
     */
    public static function fromRating(int $rating, int $minValue, int $maxValue): self
    {
        if ($maxValue <= $minValue) {
            // Defensive: a misconfigured scale degenerates to Neutral.
            return self::Neutral;
        }

        $bounded = max($minValue, min($maxValue, $rating));
        $span    = $maxValue - $minValue;
        // `+ 1` ensures the top value lands on the last bucket
        // (otherwise rating == max would round to bucket 5 only by
        // chance — the +1 makes the partition mathematically exact).
        $bucket  = (int) floor((($bounded - $minValue) * count(self::cases())) / ($span + 1));

        return match ($bucket) {
            0       => self::UnsatisfiedAtAll,
            1       => self::Unsatisfied,
            2       => self::Neutral,
            3       => self::Satisfied,
            default => self::VerySatisfied,
        };
    }

    public function labelKey(): string
    {
        return "enums.rating_sentiment.{$this->value}";
    }

    /**
     * Whether this sentiment counts as "negative" for the purposes
     * of forcing a comment. The cutoff itself is data-driven — this
     * helper just declares the canonical category for each label.
     */
    public function isNegative(): bool
    {
        return $this === self::UnsatisfiedAtAll
            || $this === self::Unsatisfied
            || $this === self::Neutral;
    }
}
