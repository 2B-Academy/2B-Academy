<?php

namespace Tests\Feature\Api\Mobile;

use App\Models\Course;
use App\Models\CourseRating;

/**
 * S-05 rating bottom sheet. Settings-driven scale (1–5) with a
 * conditional comment requirement (required when rating ≤ 3).
 */
class RatingApiTest extends MobileTestCase
{
    private function url(Course $course): string
    {
        return self::BASE . '/mobile/my-learning/courses/' . $course->id . '/rating';
    }

    /**
     * Regression: a high rating with no comment must succeed. The
     * course_ratings.comment column was NOT NULL, so MobileRatingService
     * (which stores null when no comment) crashed on this happy path.
     */
    public function test_high_rating_without_comment_succeeds(): void
    {
        $user   = $this->employee();
        $course = Course::factory()->create();

        $response = $this->withHeaders($this->headersFor($user))
                         ->postJson($this->url($course), ['rating' => 5]);

        $this->assertSuccess($response);
        $this->assertDatabaseHas('course_ratings', [
            'user_id'   => $user->id,
            'course_id' => $course->id,
            'rating'    => 5,
            'comment'   => null,
        ]);
    }

    public function test_low_rating_requires_comment(): void
    {
        $user   = $this->employee();
        $course = Course::factory()->create();

        $response = $this->withHeaders($this->headersFor($user))
                         ->postJson($this->url($course), ['rating' => 2]);

        $this->assertError($response, 422);
    }

    public function test_low_rating_with_comment_succeeds(): void
    {
        $user   = $this->employee();
        $course = Course::factory()->create();

        $response = $this->withHeaders($this->headersFor($user))
                         ->postJson($this->url($course), ['rating' => 2, 'comment' => 'Needs better pacing']);

        $this->assertSuccess($response);
        $this->assertDatabaseHas('course_ratings', [
            'user_id'   => $user->id,
            'course_id' => $course->id,
            'rating'    => 2,
            'comment'   => 'Needs better pacing',
        ]);
    }

    public function test_rating_above_max_is_rejected(): void
    {
        $user   = $this->employee();
        $course = Course::factory()->create();

        $response = $this->withHeaders($this->headersFor($user))
                         ->postJson($this->url($course), ['rating' => 6]);

        $this->assertError($response, 422);
    }

    public function test_rating_below_min_is_rejected(): void
    {
        $user   = $this->employee();
        $course = Course::factory()->create();

        $response = $this->withHeaders($this->headersFor($user))
                         ->postJson($this->url($course), ['rating' => 0]);

        $this->assertError($response, 422);
    }

    public function test_resubmitting_updates_existing_rating(): void
    {
        $user   = $this->employee();
        $course = Course::factory()->create();

        $this->withHeaders($this->headersFor($user))
             ->postJson($this->url($course), ['rating' => 4]);
        $this->withHeaders($this->headersFor($user))
             ->postJson($this->url($course), ['rating' => 5]);

        $this->assertSame(1, CourseRating::where('user_id', $user->id)->where('course_id', $course->id)->count());
        $this->assertDatabaseHas('course_ratings', [
            'user_id'   => $user->id,
            'course_id' => $course->id,
            'rating'    => 5,
        ]);
    }

    public function test_rating_missing_course_returns_404(): void
    {
        $user = $this->employee();

        $response = $this->withHeaders($this->headersFor($user))
                         ->postJson(self::BASE . '/mobile/my-learning/courses/999999/rating', ['rating' => 5]);

        $this->assertError($response, 404);
    }
}
