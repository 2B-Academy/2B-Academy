<?php

declare(strict_types=1);

namespace App\Http\Resources\Mobile;

use App\Enums\Mobile\UnitContentType;
use App\Models\CourseLecture;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * S-03 "Course Content" unit row. Maps a `course_lectures` record into
 * the mobile "Module" view-model. `badge` is the translated label of
 * `content_type` resolved through the platform enum so the client can
 * stay agnostic of the type values themselves.
 */
class AcademyCourseUnitResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var CourseLecture $lecture */
        $lecture   = $this->resource;
        $locale    = app()->getLocale();
        $type      = UnitContentType::fromLecture($lecture);

        return [
            'id'                  => (int) $lecture->id,
            'title'               => $lecture->getTranslation('title', $locale),
            'content_type'        => $type->value,
            'label_key'           => $type->labelKey(),
            'duration_minutes'    => $lecture->duration_minutes !== null
                ? (int) $lecture->duration_minutes
                : null,
            'learner_scope'       => $lecture->learner_scope ?? 'all',
            'session_id'          => $lecture->session_id !== null ? (int) $lecture->session_id : null,
            'require_completion'  => (bool) ($lecture->require_completion ?? false),
        ];
    }
}
