<?php

declare(strict_types=1);

namespace App\Http\Resources\Mobile;

use App\Enums\Mobile\RatingSentiment;
use App\Services\Mobile\MobileSettings;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * The user's own rating for a course — resolves the 1..5 numeric
 * value through the platform RatingSentiment enum (so the mobile
 * client receives a stable sentiment key, not just a raw number).
 */
class CourseRatingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $row = $this->resource;
        $settings = app(MobileSettings::class);

        $sentiment = RatingSentiment::fromRating(
            (int) ($row->rating ?? 0),
            $settings->ratingMinValue(),
            $settings->ratingMaxValue(),
        );

        return [
            'id'             => (int) $row->id,
            'rating'         => (int) $row->rating,
            'sentiment'      => $sentiment->value,
            'sentiment_key'  => $sentiment->labelKey(),
            'comment'        => $row->comment,
            'created_at'     => $row->created_at instanceof \Carbon\Carbon
                ? $row->created_at->toIso8601String()
                : $row->created_at,
        ];
    }
}
