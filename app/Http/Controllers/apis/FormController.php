<?php

namespace App\Http\Controllers\apis;

use App\Http\Requests\Api\FormQuestionRequest;
use App\Http\Requests\Api\FormRequest;
use App\Http\Resources\FormQuestionResource;
use App\Http\Resources\FormResource;
use App\Models\Form;
use App\Models\FormQuestion;
use App\Services\FormService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class FormController extends ApiController
{
    public function __construct(private readonly FormService $service) {}

    /**
     * @OA\Get(
     *     path="/forms",
     *     tags={"Forms"},
     *     summary="List forms (paginated, admin only).",
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(ref="#/components/parameters/AcceptLanguage"),
     *     @OA\Parameter(ref="#/components/parameters/Page"),
     *     @OA\Parameter(ref="#/components/parameters/PerPage"),
     *     @OA\Parameter(ref="#/components/parameters/Search"),
     *     @OA\Response(
     *         response=200,
     *         description="Paginated forms",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(@OA\Property(
     *                     property="result",
     *                     type="array",
     *                     @OA\Items(ref="#/components/schemas/Form")
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
        $forms = $this->service->paginate(
            perPage: (int) $request->get('per_page', 20),
            search:  $request->get('search'),
        );
        return $this->paginated(__('messages.retrieved'), $forms);
    }

    /**
     * @OA\Get(
     *     path="/forms/{form}",
     *     tags={"Forms"},
     *     summary="Show a form with its questions (admin only).",
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(ref="#/components/parameters/AcceptLanguage"),
     *     @OA\Parameter(name="form", in="path", required=true, @OA\Schema(type="integer", minimum=1)),
     *     @OA\Response(
     *         response=200,
     *         description="Form",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(@OA\Property(property="result", ref="#/components/schemas/Form"))
     *             }
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound")
     * )
     */
    public function show(Form $form): JsonResponse
    {
        $form = $this->service->find($form->id);
        return $this->success(__('messages.retrieved'), new FormResource($form));
    }

    /**
     * @OA\Post(
     *     path="/forms",
     *     tags={"Forms"},
     *     summary="Create a form (admin only).",
     *     security={{"BearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title","duration","full_mark"},
     *             @OA\Property(property="title",     ref="#/components/schemas/TranslatedString"),
     *             @OA\Property(property="duration",  type="integer", minimum=1, description="Duration in minutes."),
     *             @OA\Property(property="full_mark", type="integer", minimum=1),
     *             @OA\Property(property="active",    type="boolean", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Created",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(@OA\Property(property="result", ref="#/components/schemas/Form"))
     *             }
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=422, ref="#/components/responses/ValidationError")
     * )
     */
    public function store(FormRequest $request): JsonResponse
    {
        $form = $this->service->create($request->validated());
        return $this->created(__('messages.created'), new FormResource($form));
    }

    /**
     * @OA\Put(
     *     path="/forms/{form}",
     *     tags={"Forms"},
     *     summary="Update a form (admin only).",
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(name="form", in="path", required=true, @OA\Schema(type="integer", minimum=1)),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="title",     ref="#/components/schemas/TranslatedString"),
     *             @OA\Property(property="duration",  type="integer", minimum=1),
     *             @OA\Property(property="full_mark", type="integer", minimum=1),
     *             @OA\Property(property="active",    type="boolean", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Updated",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(@OA\Property(property="result", ref="#/components/schemas/Form"))
     *             }
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=422, ref="#/components/responses/ValidationError")
     * )
     */
    public function update(Form $form, FormRequest $request): JsonResponse
    {
        $updated = $this->service->update($form, $request->validated());
        return $this->success(__('messages.updated'), new FormResource($updated));
    }

    /**
     * @OA\Delete(
     *     path="/forms/{form}",
     *     tags={"Forms"},
     *     summary="Delete a form (admin only).",
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(name="form", in="path", required=true, @OA\Schema(type="integer", minimum=1)),
     *     @OA\Response(response=200, description="Deleted", @OA\JsonContent(ref="#/components/schemas/EmptyResponse")),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound")
     * )
     */
    public function destroy(Form $form): JsonResponse
    {
        $this->service->delete($form);
        return $this->deleted(__('messages.deleted'));
    }

    /**
     * @OA\Post(
     *     path="/forms/{form}/questions",
     *     tags={"Forms"},
     *     summary="Add a question to a form (admin only).",
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(name="form", in="path", required=true, @OA\Schema(type="integer", minimum=1)),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"type","question"},
     *             @OA\Property(property="type",     type="string", enum={"radio","yes_no","text"}),
     *             @OA\Property(property="question", ref="#/components/schemas/TranslatedString"),
     *             @OA\Property(
     *                 property="answers",
     *                 type="array",
     *                 description="Required for non-text types. Minimum of two answers.",
     *                 @OA\Items(
     *                     type="object",
     *                     required={"answer","is_true"},
     *                     @OA\Property(property="answer",  ref="#/components/schemas/TranslatedString"),
     *                     @OA\Property(property="is_true", type="boolean")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Question created",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(@OA\Property(property="result", ref="#/components/schemas/FormQuestion"))
     *             }
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=422, ref="#/components/responses/ValidationError")
     * )
     */
    public function addQuestion(Form $form, FormQuestionRequest $request): JsonResponse
    {
        $question = $this->service->addQuestion($form, $request->validated());
        return $this->created(__('messages.created'), new FormQuestionResource($question));
    }

    /**
     * @OA\Delete(
     *     path="/forms/{form}/questions/{question}",
     *     tags={"Forms"},
     *     summary="Delete a form question (admin only).",
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(name="form", in="path", required=true, @OA\Schema(type="integer", minimum=1)),
     *     @OA\Parameter(
     *         name="question",
     *         in="path",
     *         required=true,
     *         description="Question identifier.",
     *         @OA\Schema(type="integer", minimum=1)
     *     ),
     *     @OA\Response(response=200, description="Deleted", @OA\JsonContent(ref="#/components/schemas/EmptyResponse")),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound")
     * )
     */
    public function destroyQuestion(Form $form, FormQuestion $question): JsonResponse
    {
        abort_if($question->form_id !== $form->id, 404);
        $this->service->deleteQuestion($question);
        return $this->deleted(__('messages.deleted'));
    }
}
