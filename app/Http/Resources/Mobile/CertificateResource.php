<?php

declare(strict_types=1);

namespace App\Http\Resources\Mobile;

use App\Models\UserCertificate;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * S-07 certificate card.
 *
 * The client only ever sees the certificate's own identity — integer
 * `id`, public `uuid`, and human `certificate_number`. The originating
 * exam/evaluation (`source_type`/`source_id`) is NEVER exposed: the
 * mobile app must not know how a certificate was earned.
 *
 * @mixin UserCertificate
 */
class CertificateResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var UserCertificate $certificate */
        $certificate = $this->resource;

        $locale = app()->getLocale();
        $title  = $certificate->localizedCourseTitle($locale);

        return [
            'id'                 => (int) $certificate->id,
            'uuid'               => $certificate->uuid,
            'certificate_number' => $certificate->certificate_number,
            'status'             => $certificate->status,
            'course'             => [
                'id'    => (int) $certificate->course_id,
                'title' => $title,
            ],
            // Flat aliases retained for existing mobile cards.
            'course_id'          => (int) $certificate->course_id,
            'course_title'       => $title,
            'issued_at'          => optional($certificate->issued_at)->toIso8601String(),
            'issued_date'        => optional($certificate->issued_at)->toDateString(),
            'learner'            => $request->user()
                ? (new LearnerIdentityResource($request->user()))->toArray($request)
                : null,
        ];
    }
}
