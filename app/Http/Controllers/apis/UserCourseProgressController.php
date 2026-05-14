<?php

namespace App\Http\Controllers\apis;

use App\Services\UserCourseProgressService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserCourseProgressController extends ApiController
{
    public function __construct(private readonly UserCourseProgressService $service) {}

    public function index(Request $request): JsonResponse
    {
        $results = $this->service->paginate(
            (int) $request->get('per_page', 20),
            $request->integer('course_id') ?: null,
            $request->integer('group_id')  ?: null,
            $request->integer('user_id')   ?: null,
        );

        return $this->paginated(__('messages.retrieved'), $results);
    }
}
