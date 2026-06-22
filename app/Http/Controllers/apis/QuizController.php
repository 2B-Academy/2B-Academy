<?php

namespace App\Http\Controllers\apis;

use App\Models\UserExam;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QuizController extends ApiController
{
    /**
     * Paginated list of all user exam attempts (admin only).
     * Filters: ?course_id, ?user_id, ?status (success|fail), ?search (learner name)
     */
    public function index(Request $request): JsonResponse
    {
        $perPage  = (int) $request->get('per_page', 20);
        $courseId = $request->get('course_id') ? (int) $request->get('course_id') : null;
        $userId   = $request->get('user_id')   ? (int) $request->get('user_id')   : null;
        $status   = $request->get('status');
        $search   = $request->get('search');

        $exams = UserExam::query()
            ->with([
                'user:id,name',
                'course:id,title',
                'exam:id,title,degree',
            ])
            ->when($courseId, fn ($q) => $q->where('course_id', $courseId))
            ->when($userId,   fn ($q) => $q->where('user_id', $userId))
            ->when($status,   fn ($q) => $q->where('status', $status))
            ->when($search,   fn ($q) => $q->whereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%")))
            ->latest()
            ->paginate($perPage);

        $locale   = app()->getLocale();
        $resource = $exams->through(fn ($exam) => [
            'id'           => $exam->id,
            'user'         => [
                'id'   => $exam->user?->id,
                'name' => $exam->user?->name,
            ],
            'course'       => [
                'id'    => $exam->course?->id,
                'title' => $exam->course?->getTranslation('title', $locale),
            ],
            'exam'         => $exam->exam ? [
                'id'     => $exam->exam->id,
                'title'  => $exam->exam->getTranslation('title', $locale),
                'degree' => $exam->exam->degree,
            ] : null,
            'user_degree'  => $exam->user_degree,
            'status'       => $exam->status,
            'created_at'   => $exam->created_at?->toDateTimeString(),
        ]);

        return $this->paginated(__('messages.retrieved'), $resource);
    }

    /**
     * Show a single quiz attempt with full answer breakdown (admin only).
     */
    public function show(UserExam $userExam): JsonResponse
    {
        $userExam->load(['user:id,name', 'course:id,title', 'exam:id,title,degree', 'answers']);

        $locale = app()->getLocale();

        return $this->success(__('messages.retrieved'), [
            'id'          => $userExam->id,
            'user'        => [
                'id'   => $userExam->user?->id,
                'name' => $userExam->user?->name,
            ],
            'course'      => $userExam->course ? [
                'id'    => $userExam->course->id,
                'title' => $userExam->course->getTranslation('title', $locale),
            ] : null,
            'exam'        => $userExam->exam ? [
                'id'     => $userExam->exam->id,
                'title'  => $userExam->exam->getTranslation('title', $locale),
                'degree' => $userExam->exam->degree,
            ] : null,
            'user_degree' => $userExam->user_degree,
            'status'      => $userExam->status,
            'answers'     => $userExam->answers->map(fn ($a) => [
                'question'   => $a->question,
                'answer'     => $a->answer,
                'is_correct' => (bool) $a->is_correct,
            ]),
            'created_at'  => $userExam->created_at?->toDateTimeString(),
        ]);
    }
}
