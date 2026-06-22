<?php

namespace App\Http\Controllers\apis;

use App\Http\Resources\AuditLogResource;
use App\Services\AuditLogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuditLogController extends ApiController
{
    public function __construct(private readonly AuditLogService $service) {}

    public function index(Request $request): JsonResponse
    {
        $logs = $this->service->list(
            perPage: (int) $request->get('per_page', 20),
            search:  $request->get('search'),
            action:  $request->get('action'),
        );

        return $this->paginated(__('messages.retrieved'), AuditLogResource::collection($logs));
    }
}
