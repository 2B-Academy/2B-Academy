<?php

namespace App\Http\Controllers\apis;

use App\Services\CertificateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CertificateController extends ApiController
{
    public function __construct(private readonly CertificateService $service) {}

    public function index(Request $request): JsonResponse
    {
        $certificates = $this->service->paginate(
            (int) $request->get('per_page', 20),
            $request->integer('course_id') ?: null
        );

        return $this->paginated(__('messages.retrieved'), $certificates);
    }
}
