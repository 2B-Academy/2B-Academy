<?php

declare(strict_types=1);

namespace App\Enums\Mobile;

use App\Models\CourseLecture;

/**
 * Maps a `CourseLecture.content_type` value to the badge variant
 * surfaced on the S-03 Course Content list. Values are derived from
 * the actual `course_lectures.content_type` column so the catalogue
 * stays the source of truth — the enum exists purely so the mobile
 * resource can hand the client a stable key it can drive its own
 * iconography from without parsing free-form strings.
 *
 * The `label()` returns a human translation, NOT a hardcoded English
 * string — the caller hydrates the locale via `__($enum->labelKey())`.
 */
enum UnitContentType: string
{
    case Video      = 'video';
    case Reading    = 'reading';
    case Document   = 'document';
    case Quiz       = 'quiz';
    case Assignment = 'assignment';
    case Session    = 'session';
    case Live       = 'live';
    case Other      = 'other';

    /**
     * Resolve from whatever the catalogue stored. Unknown values map
     * to `Other` so the mobile client always gets a usable badge.
     */
    public static function fromCatalogue(?string $rawType): self
    {
        if ($rawType === null || $rawType === '') {
            return self::Other;
        }

        $normalised = strtolower(trim($rawType));

        return match ($normalised) {
            'video'                          => self::Video,
            'reading', 'article', 'text'     => self::Reading,
            'document', 'pdf', 'file'        => self::Document,
            'quiz', 'exam', 'test'           => self::Quiz,
            'assignment', 'homework'         => self::Assignment,
            'session', 'live_session', 'webinar', 'meeting' => self::Session,
            'live'                           => self::Live,
            default                          => self::Other,
        };
    }

    public static function fromLecture(CourseLecture $lecture): self
    {
        return self::fromCatalogue($lecture->content_type ?? null);
    }

    public function labelKey(): string
    {
        return "enums.unit_content_type.{$this->value}";
    }
}
