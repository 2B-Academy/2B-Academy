<?php

namespace App\Http\Controllers\apis;

use App\Http\Requests\Api\SubmitFormAnswersRequest;
use App\Http\Resources\FormResource;
use App\Models\Form;
use App\Services\UserFormSubmissionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class UserFormController extends ApiController
{
    public function __construct(private readonly UserFormSubmissionService $formService) {}

    /**
     * @OA\Get(
     *     path="/forms/{formUuid}/start",
     *     tags={"User Forms"},
     *     summary="Start (or resume) a form session for the authenticated user.",
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(ref="#/components/parameters/AcceptLanguage"),
     *     @OA\Parameter(
     *         name="formUuid",
     *         in="path",
     *         required=true,
     *         description="Form UUID",
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Form with questions and session timing",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(@OA\Property(
     *                     property="result",
     *                     type="object",
     *                     @OA\Property(property="form", ref="#/components/schemas/Form"),
     *                     @OA\Property(
     *                         property="session",
     *                         type="object",
     *                         @OA\Property(property="start_at",  type="string", format="date-time", nullable=true),
     *                         @OA\Property(property="end_at",    type="string", format="date-time", nullable=true),
     *                         @OA\Property(property="submitted", type="boolean")
     *                     )
     *                 ))
     *             }
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound")
     * )
     */
    public function start(Request $request, string $formUuid): JsonResponse
    {
        $form = Form::where('uuid', $formUuid)->where('active', true)
            ->with('questions.answers')
            ->withCount('questions')
            ->firstOrFail();

        $userForm = $this->formService->startForm($request->user(), $form);

        return $this->success(__('messages.retrieved'), [
            'form'     => new FormResource($form),
            'session'  => [
                'start_at' => $userForm->start_at,
                'end_at'   => $userForm->end_at,
                'submitted' => $this->formService->hasSubmitted($request->user()->id, $form->id),
            ],
        ]);
    }

    /**
     * @OA\Post(
     *     path="/forms/{formUuid}/submit",
     *     tags={"User Forms"},
     *     summary="Submit form answers for the authenticated user. Auto-grades MCQ; stores text answers as correct.",
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(
     *         name="formUuid",
     *         in="path",
     *         required=true,
     *         description="Form UUID",
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"questions"},
     *             @OA\Property(
     *                 property="questions",
     *                 type="array",
     *                 minItems=1,
     *                 @OA\Items(
     *                     type="object",
     *                     required={"question_id","question_title","answer_id"},
     *                     @OA\Property(property="question_id",    type="integer", example=12),
     *                     @OA\Property(property="question_title", type="string",  example="What is your name?"),
     *                     @OA\Property(property="answer_id",      description="Selected answer id (integer for MCQ) or free-text answer (string).", example=4)
     *                 )
     *             ),
     *             @OA\Property(property="minutes_remaining", type="integer", minimum=0, example=5)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Form submitted and graded",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(@OA\Property(
     *                     property="result",
     *                     type="object",
     *                     @OA\Property(property="mark",     type="integer", example=8),
     *                     @OA\Property(property="duration", type="integer", example=25, description="Total minutes spent on the form.")
     *                 ))
     *             }
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(
     *         response=409,
     *         description="Form already submitted by this user",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     *     @OA\Response(response=422, ref="#/components/responses/ValidationError")
     * )
     */
    public function submit(SubmitFormAnswersRequest $request, string $formUuid): JsonResponse
    {
        $form = Form::where('uuid', $formUuid)->where('active', true)->firstOrFail();

        if ($this->formService->hasSubmitted($request->user()->id, $form->id)) {
            return $this->error(__('messages.form_already_submitted'), 409);
        }

        $validated = $request->validated();

        $userForm = $this->formService->submitForm(
            $request->user(),
            $form,
            $validated['questions'],
            (int) ($validated['minutes_remaining'] ?? 0),
        );

        return $this->success(__('messages.created'), [
            'mark'     => $userForm->mark,
            'duration' => $userForm->duration,
        ]);
    }
}
