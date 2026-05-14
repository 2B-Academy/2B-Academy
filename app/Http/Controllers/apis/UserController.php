<?php

namespace App\Http\Controllers\apis;

use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends ApiController
{
    public function __construct(private readonly UserService $userService) {}

    public function index(Request $request): JsonResponse
    {
        $users = $this->userService->list(
            perPage: (int) $request->get('per_page', 20),
            search:  $request->get('search'),
        );

        return $this->paginated(__('messages.retrieved'), UserResource::collection($users));
    }

    public function show(User $user): JsonResponse
    {
        $user = $this->userService->getUserWithActivity($user->id);

        return $this->success(
            __('messages.retrieved'),
            new UserResource($user),
        );
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $user = $this->userService->create($data);

        return $this->created(
            __('messages.created'),
            new UserResource($user),
        );
    }

    public function destroy(User $user): JsonResponse
    {
        $this->userService->delete($user);
        return $this->deleted();
    }

    /** Lightweight user list for select2 / dropdowns. */
    public function search(Request $request): JsonResponse
    {
        $users = $this->userService->list(100, $request->get('q'));

        return $this->success(
            __('messages.retrieved'),
            UserResource::collection($users),
        );
    }
}
