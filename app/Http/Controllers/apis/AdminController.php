<?php

namespace App\Http\Controllers\apis;

use App\Http\Requests\Api\AdminRequest;
use App\Http\Resources\AdminResource;
use App\Models\Admin;
use App\Services\AdminService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class AdminController extends ApiController
{
    public function __construct(private readonly AdminService $service) {}

    /**
     * @OA\Get(
     *     path="/admins",
     *     tags={"Admins"},
     *     summary="List admins (paginated).",
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(ref="#/components/parameters/AcceptLanguage"),
     *     @OA\Parameter(ref="#/components/parameters/Page"),
     *     @OA\Parameter(ref="#/components/parameters/PerPage"),
     *     @OA\Parameter(ref="#/components/parameters/Search"),
     *     @OA\Response(
     *         response=200,
     *         description="Paginated admins",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(@OA\Property(
     *                     property="result",
     *                     type="array",
     *                     @OA\Items(ref="#/components/schemas/Admin")
     *                 ))
     *             }
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden")
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $admins = $this->service->paginate(
            (int) $request->get('per_page', 20),
            $request->get('search')
        );
        return $this->paginated(__('messages.retrieved'), $admins);
    }

    /**
     * @OA\Get(
     *     path="/admins/{admin}",
     *     tags={"Admins"},
     *     summary="Show an admin (with roles).",
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(ref="#/components/parameters/AcceptLanguage"),
     *     @OA\Parameter(name="admin", in="path", required=true, @OA\Schema(type="integer", minimum=1)),
     *     @OA\Response(
     *         response=200,
     *         description="Admin detail",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(@OA\Property(property="result", ref="#/components/schemas/Admin"))
     *             }
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound")
     * )
     */
    public function show(Admin $admin): JsonResponse
    {
        $admin = $this->service->findWithRoles($admin->id);
        return $this->success(__('messages.retrieved'), new AdminResource($admin));
    }

    /**
     * @OA\Post(
     *     path="/admins",
     *     tags={"Admins"},
     *     summary="Create an admin.",
     *     security={{"BearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","password","password_confirmation","role"},
     *             @OA\Property(property="name",                  type="string", maxLength=255),
     *             @OA\Property(property="email",                 type="string", format="email"),
     *             @OA\Property(property="password",              type="string", format="password", minLength=8),
     *             @OA\Property(property="password_confirmation", type="string", format="password"),
     *             @OA\Property(property="role",                  type="string", description="Existing role name (Spatie).")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Created",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(@OA\Property(property="result", ref="#/components/schemas/Admin"))
     *             }
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=422, ref="#/components/responses/ValidationError")
     * )
     */
    public function store(AdminRequest $request): JsonResponse
    {
        $admin = $this->service->create($request->validated());
        return $this->created(__('messages.created'), new AdminResource($admin));
    }

    /**
     * @OA\Put(
     *     path="/admins/{admin}",
     *     tags={"Admins"},
     *     summary="Update an admin.",
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(name="admin", in="path", required=true, @OA\Schema(type="integer", minimum=1)),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","role"},
     *             @OA\Property(property="name",                  type="string", maxLength=255),
     *             @OA\Property(property="email",                 type="string", format="email"),
     *             @OA\Property(property="password",              type="string", format="password", minLength=8, nullable=true),
     *             @OA\Property(property="password_confirmation", type="string", format="password", nullable=true),
     *             @OA\Property(property="role",                  type="string", description="Existing role name (Spatie).")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Updated",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(@OA\Property(property="result", ref="#/components/schemas/Admin"))
     *             }
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=422, ref="#/components/responses/ValidationError")
     * )
     */
    public function update(Admin $admin, AdminRequest $request): JsonResponse
    {
        $admin = $this->service->update($admin, $request->validated());
        return $this->success(__('messages.updated'), new AdminResource($admin));
    }

    /**
     * @OA\Delete(
     *     path="/admins/{admin}",
     *     tags={"Admins"},
     *     summary="Delete an admin.",
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(name="admin", in="path", required=true, @OA\Schema(type="integer", minimum=1)),
     *     @OA\Response(response=200, description="Deleted", @OA\JsonContent(ref="#/components/schemas/EmptyResponse")),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound")
     * )
     */
    public function destroy(Admin $admin): JsonResponse
    {
        $this->service->delete($admin);
        return $this->deleted(__('messages.deleted'));
    }
}
