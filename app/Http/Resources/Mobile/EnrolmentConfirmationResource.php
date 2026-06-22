<?php

declare(strict_types=1);

namespace App\Http\Resources\Mobile;

use App\Enums\Mobile\EnrolmentOutcome;
use App\Models\Course;
use App\Models\CourseSection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * S-04 confirmation screen. Carries the outcome + a snapshot of the
 * cohort the user landed in, plus the `learner` identity block (with
 * `machine_code`) so the confirmation receipt can be cross-referenced
 * against HR records.
 *
 * The mobile client uses `outcome` to decide between the green / red /
 * amber confirmation layouts.
 */
class EnrolmentConfirmationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();

        /** @var EnrolmentOutcome $outcome */
        $outcome = $this->resource['outcome'];
        /** @var CourseSection|null $cohort */
        $cohort  = $this->resource['cohort'] ?? null;
        /** @var Course|null $course */
        $course  = $this->resource['course'] ?? null;

        return [
            'outcome'      => $outcome->value,
            'is_success'   => $outcome->isSuccess(),
            'message_key'  => $outcome->messageKey(),

            'learner' => $request->user()
                ? (new LearnerIdentityResource($request->user()))->toArray($request)
                : null,

            'course' => $course ? [
                'id'    => (int) $course->id,
                'title' => $course->getTranslation('title', $locale),
                'image' => $course->image,
            ] : null,

            'cohort' => $cohort ? [
                'id'                  => (int) $cohort->id,
                'name'                => $cohort->getTranslation('name', $locale),
                'start_date'          => $cohort->start_date instanceof \Carbon\Carbon
                    ? $cohort->start_date->format('Y-m-d')
                    : $cohort->start_date,
                'end_date'            => $cohort->end_date instanceof \Carbon\Carbon
                    ? $cohort->end_date->format('Y-m-d')
                    : $cohort->end_date,
                'capacity'            => $cohort->capacity !== null ? (int) $cohort->capacity : null,
                'enrolment_closes_at' => $cohort->enrolment_closes_at instanceof \Carbon\Carbon
                    ? $cohort->enrolment_closes_at->format('Y-m-d')
                    : $cohort->enrolment_closes_at,
            ] : null,
        ];
    }
}
