<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Resources\Mobile\CertificateResource;
use App\Http\Traits\HelperTrait;
use App\Services\Mobile\MobileCertificateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Mobile S-07 — Certificates. The list is served by MyLearningController;
 * this controller owns the per-certificate detail / download.
 *
 * Certificates are first-class entities now: every lookup is by the
 * certificate's own integer id (scoped to the authenticated learner).
 * The old compound-id (`exam:123`) routing is gone.
 *
 * 📱 MOBILE — Employee/Learner mobile app. Grouped under the single
 * `Mobile` Swagger tag (registered globally in App\OpenApi\Info).
 */
class CertificateController extends MobileBaseController
{
    use HelperTrait;

    public function __construct(private readonly MobileCertificateService $certificates) {}

    /**
     * @OA\Get(
     *     path="/mobile/certificates/{certificateId}",
     *     tags={"Mobile"},
     *     summary="📱 [MOBILE · S-07] Single certificate detail",
     *     description="📱 **MOBILE** · Screen **S-07 — Certificates · detail tap** · Audience: Employee/Learner mobile app · Looks up a certificate by its own integer id (scoped to the learner).",
     *     @OA\Parameter(ref="#/components/parameters/MobileAuthorization"),
     *     @OA\Parameter(ref="#/components/parameters/EmployeeCode"),
     *     @OA\Parameter(ref="#/components/parameters/AcceptLanguage"),
     *     @OA\Parameter(name="certificateId", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="OK"),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function show(Request $request, int $certificateId): JsonResponse
    {
        $certificate = $this->certificates->findById($request->user(), $certificateId, app()->getLocale());
        if ($certificate === null) {
            return $this->notFound(__('messages.mobile.certificate_not_found'));
        }

        return $this->success(
            __('messages.mobile.certificate_download_ready'),
            new CertificateResource($certificate),
        );
    }

    /**
     * @OA\Get(
     *     path="/mobile/certificates/{certificateId}/download",
     *     tags={"Mobile"},
     *     summary="📱 [MOBILE · S-07] Download certificate image",
     *     description="📱 **MOBILE** · Screen **S-07 — Certificates · Download** · Audience: Employee/Learner mobile app · Returns the rendered base64 JPEG for the certificate.",
     *     @OA\Parameter(ref="#/components/parameters/MobileAuthorization"),
     *     @OA\Parameter(ref="#/components/parameters/EmployeeCode"),
     *     @OA\Parameter(ref="#/components/parameters/AcceptLanguage"),
     *     @OA\Parameter(name="certificateId", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="OK"),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function download(Request $request, int $certificateId): JsonResponse
    {
        $user        = $request->user();
        $certificate = $this->certificates->findById($user, $certificateId, app()->getLocale());
        if ($certificate === null) {
            return $this->notFound(__('messages.mobile.certificate_not_found'));
        }

        // Re-use the legacy template renderer so the mobile / web
        // certificate visuals stay byte-identical.
        $image = $this->generateCertificate(
            $certificate->localizedCourseTitle(app()->getLocale()),
            $user->name,
        );

        return $this->success(
            __('messages.mobile.certificate_download_ready'),
            [
                'id'                 => (int) $certificate->id,
                'certificate_number' => $certificate->certificate_number,
                'course_id'          => (int) $certificate->course_id,
                'course_title'       => $certificate->localizedCourseTitle(app()->getLocale()),
                'issued_at'          => optional($certificate->issued_at)->toIso8601String(),
                'image_base64'       => $image,
                'mime_type'          => 'image/jpeg',
            ],
        );
    }
}
