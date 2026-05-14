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

class FormController extends ApiController
{
    public function __construct(private readonly FormService $service) {}

    public function index(Request $request): JsonResponse
    {
        $forms = $this->service->paginate(
            perPage: (int) $request->get('per_page', 20),
            search:  $request->get('search'),
        );
        return $this->paginated(__('messages.retrieved'), $forms);
    }

    public function show(Form $form): JsonResponse
    {
        $form->load('questions.answers');
        return $this->success(__('messages.retrieved'), new FormResource($form));
    }

    public function store(FormRequest $request): JsonResponse
    {
        $form = $this->service->create($request->validated());
        return $this->created(__('messages.created'), new FormResource($form));
    }

    public function update(Form $form, FormRequest $request): JsonResponse
    {
        $updated = $this->service->update($form, $request->validated());
        return $this->success(__('messages.updated'), new FormResource($updated));
    }

    public function destroy(Form $form): JsonResponse
    {
        $this->service->delete($form);
        return $this->deleted(__('messages.deleted'));
    }

    // POST /forms/{form}/questions
    public function addQuestion(Form $form, FormQuestionRequest $request): JsonResponse
    {
        $question = $this->service->addQuestion($form, $request->validated());
        return $this->created(__('messages.created'), new FormQuestionResource($question));
    }

    // DELETE /forms/{form}/questions/{question}
    public function destroyQuestion(Form $form, FormQuestion $question): JsonResponse
    {
        abort_if($question->form_id !== $form->id, 404);
        $this->service->deleteQuestion($question);
        return $this->deleted(__('messages.deleted'));
    }
}
