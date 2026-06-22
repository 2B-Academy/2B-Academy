<?php

namespace App\Http\Traits;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

trait ApiResponse
{
    protected function success(string $message, mixed $data = null, int $status = Response::HTTP_OK): JsonResponse
    {
        $payload = ['status' => 'success', 'message' => $message];
        if (!is_null($data)) {
            $payload['result'] = $data;
        }
        return response()->json($payload, $status);
    }

    protected function created(string $message, mixed $data = null): JsonResponse
    {
        return $this->success($message, $data, Response::HTTP_CREATED);
    }

    protected function deleted(string $message = ''): JsonResponse
    {
        return $this->success($message ?: __('messages.deleted'));
    }

    protected function error(string $message, int $status = Response::HTTP_BAD_REQUEST, array $errors = []): JsonResponse
    {
        $payload = ['status' => 'error', 'message' => $message];
        if ($errors !== []) {
            $payload['errors'] = $errors;
        }
        return response()->json($payload, $status);
    }

    protected function notFound(string $message = ''): JsonResponse
    {
        return $this->error($message ?: __('messages.not_found'), Response::HTTP_NOT_FOUND);
    }

    protected function forbidden(string $message = ''): JsonResponse
    {
        return $this->error($message ?: __('messages.forbidden'), Response::HTTP_FORBIDDEN);
    }

    protected function unauthorized(string $message = ''): JsonResponse
    {
        return $this->error($message ?: __('messages.unauthenticated'), Response::HTTP_UNAUTHORIZED);
    }

    protected function paginated(string $message, mixed $resource): JsonResponse
    {
        $paginator = $resource instanceof \Illuminate\Http\Resources\Json\ResourceCollection
            ? $resource->resource
            : $resource;

        return response()->json([
            'status'  => 'success',
            'message' => $message,
            'result'  => $resource,
            'meta'    => [
                'current_page' => $paginator->currentPage(),
                'last_page'    => $paginator->lastPage(),
                'per_page'     => $paginator->perPage(),
                'total'        => $paginator->total(),
            ],
        ]);
    }
}
