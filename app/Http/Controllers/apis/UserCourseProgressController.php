<?php

namespace App\Http\Controllers\apis;

use App\Services\UserCourseProgressService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class UserCourseProgressController extends ApiController
{
    public function __construct(private readonly UserCourseProgressService $service) {}

    /**
     * @OA\Get(
     *     path="/progress",
     *     tags={"Progress"},
     *     summary="Admin: paginated overview of user course progress, with optional filters.",
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(ref="#/components/parameters/AcceptLanguage"),
     *     @OA\Parameter(ref="#/components/parameters/Page"),
     *     @OA\Parameter(ref="#/components/parameters/PerPage"),
     *     @OA\Parameter(
     *         name="course_id",
     *         in="query",
     *         required=false,
     *         description="Filter by course id.",
     *         @OA\Schema(type="integer", minimum=1)
     *     ),
     *     @OA\Parameter(
     *         name="group_id",
     *         in="query",
     *         required=false,
     *         description="Filter by course section (group) id.",
     *         @OA\Schema(type="integer", minimum=1)
     *     ),
     *     @OA\Parameter(
     *         name="user_id",
     *         in="query",
     *         required=false,
     *         description="Filter by user id.",
     *         @OA\Schema(type="integer", minimum=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Paginated user course progress rows",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(@OA\Property(
     *                     property="result",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="user_id",          type="integer"),
     *                         @OA\Property(property="user_name",        type="string"),
     *                         @OA\Property(property="course_id",        type="integer"),
     *                         @OA\Property(property="course_title",    type="string", description="Localized course title."),
     *                         @OA\Property(property="overall_progress", type="integer", example=75)
     *                     )
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
        $results = $this->service->paginate(
            (int) $request->get('per_page', 20),
            $request->integer('course_id') ?: null,
            $request->integer('group_id')  ?: null,
            $request->integer('user_id')   ?: null,
        );

        return $this->paginated(__('messages.retrieved'), $results);
    }
}
