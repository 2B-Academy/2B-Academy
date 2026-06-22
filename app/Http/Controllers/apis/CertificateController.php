<?php

namespace App\Http\Controllers\apis;

use App\Services\CertificateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class CertificateController extends ApiController
{
    public function __construct(private readonly CertificateService $service) {}

    /**
     * @OA\Get(
     *     path="/certificates",
     *     tags={"Certificates"},
     *     summary="Admin: paginated list of issued certificates, optionally filtered by course.",
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(ref="#/components/parameters/AcceptLanguage"),
     *     @OA\Parameter(ref="#/components/parameters/Page"),
     *     @OA\Parameter(ref="#/components/parameters/PerPage"),
     *     @OA\Parameter(
     *         name="course_id",
     *         in="query",
     *         required=false,
     *         description="Filter certificates by course id.",
     *         @OA\Schema(type="integer", minimum=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Paginated certificates",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(@OA\Property(
     *                     property="result",
     *                     type="array",
     *                     @OA\Items(ref="#/components/schemas/Certificate")
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
        $certificates = $this->service->paginate(
            (int) $request->get('per_page', 20),
            $request->integer('course_id') ?: null
        );

        return $this->paginated(__('messages.retrieved'), $certificates);
    }

    /**
     * @OA\Get(
     *     path="/certificates/{courseId}",
     *     tags={"Certificates"},
     *     summary="Admin: list all certificates issued for a specific course.",
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(ref="#/components/parameters/AcceptLanguage"),
     *     @OA\Parameter(
     *         name="courseId",
     *         in="path",
     *         required=true,
     *         description="Course identifier",
     *         @OA\Schema(type="integer", minimum=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Certificates for the course",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(@OA\Property(
     *                     property="result",
     *                     type="array",
     *                     @OA\Items(ref="#/components/schemas/Certificate")
     *                 ))
     *             }
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound")
     * )
     */
    public function show(int $courseId): JsonResponse
    {
        $result = $this->service->findByCourse($courseId);

        if (empty($result)) {
            return $this->notFound();
        }

        return $this->success(__('messages.retrieved'), $result);
    }
}
