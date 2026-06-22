<?php

namespace App\Http\Controllers\apis\Admin;

use App\Http\Controllers\apis\ApiController;
use App\Services\Admin\AdminCertificateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Admin endpoints for the 2026 Certificates redesign.
 *
 * Strictly additive. The legacy `/api/v1/certificates` endpoint
 * (CertificateController) is left untouched and continues to serve any
 * existing consumer.
 *
 * Routes (see routes/apis/admin-certificates.php):
 *   GET    /api/v1/admin/certificates/template/overview
 *   POST   /api/v1/admin/certificates/template
 *   GET    /api/v1/admin/certificates/template/file
 *   GET    /api/v1/admin/certificates
 *   GET    /api/v1/admin/certificates/{userId}/{courseId}/download
 */
class AdminCertificateController extends ApiController
{
    public function __construct(private readonly AdminCertificateService $service) {}

    /** GET /api/v1/admin/certificates/template/overview */
    public function templateOverview(): JsonResponse
    {
        return $this->success(__('messages.retrieved'), $this->service->overview());
    }

    /** POST /api/v1/admin/certificates/template (multipart, `file` field) */
    public function uploadTemplate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'file' => ['required', 'file', 'mimetypes:image/jpeg,image/png,image/webp,application/pdf', 'max:8192'],
        ]);

        $overview = $this->service->uploadTemplate(
            $request->file('file'),
            $request->user(),
        );

        return $this->success(__('messages.updated'), $overview);
    }

    /** GET /api/v1/admin/certificates/template/file — streams the file binary. */
    public function templateFile(): StreamedResponse|JsonResponse
    {
        $template = $this->service->activeTemplate();

        if (!$template || empty($template->file_path)) {
            return $this->notFound();
        }

        $disk = config('filesystems.default') === 's3' ? 's3' : 'public';

        if (!Storage::disk($disk)->exists($template->file_path)) {
            return $this->notFound();
        }

        return Storage::disk($disk)->response(
            $template->file_path,
            $template->original_filename ?: 'certificate-template',
            [
                'Content-Type'  => $template->mime_type ?: 'application/octet-stream',
                // Never cache: the active template can be swapped at any
                // time and the URL is stable, so any caching layer would
                // serve a stale image after a fresh upload.
                'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
                'Pragma'        => 'no-cache',
                'Expires'       => '0',
            ],
        );
    }

    /** GET /api/v1/admin/certificates — paginated issued certificates. */
    public function index(Request $request): JsonResponse
    {
        $paginator = $this->service->paginateIssued(
            (int) $request->get('per_page', 20),
            $request->get('search'),
            $request->integer('course_id') ?: null,
        );

        return $this->paginated(__('messages.retrieved'), $paginator);
    }

    /** GET /api/v1/admin/certificates/{userId}/{courseId}/download */
    public function downloadIssued(int $userId, int $courseId): Response
    {
        $cert = $this->service->renderIssuedCertificate($userId, $courseId);

        return response($cert['binary'], 200, [
            'Content-Type'        => 'image/jpeg',
            'Content-Disposition' => 'attachment; filename="'.$cert['filename'].'"',
            'Cache-Control'       => 'private, max-age=0, no-store',
        ]);
    }

    /** GET /api/v1/admin/certificates/{certificate}/download — download by certificate id. */
    public function download(int $certificate): Response
    {
        $cert = $this->service->renderById($certificate);

        return response($cert['binary'], 200, [
            'Content-Type'        => 'image/jpeg',
            'Content-Disposition' => 'attachment; filename="'.$cert['filename'].'"',
            'Cache-Control'       => 'private, max-age=0, no-store',
        ]);
    }

    /** POST /api/v1/admin/certificates/{certificate}/revoke — revoke (keeps history). */
    public function revoke(Request $request, int $certificate): JsonResponse
    {
        $row = $this->service->revoke($certificate, $request->user());

        return $this->success(__('messages.certificate_revoked'), $row);
    }
}
