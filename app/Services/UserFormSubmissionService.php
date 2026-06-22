<?php

namespace App\Services;

use App\Models\Form;
use App\Models\FormQuestionAnswer;
use App\Models\User;
use App\Models\UserForm;
use Carbon\Carbon;

class UserFormSubmissionService
{
    /**
     * Start (or re-fetch) a form session for the user.
     * Mirrors FrontControllers\FormController::index().
     */
    public function startForm(User $user, Form $form): UserForm
    {
        $userForm = UserForm::firstOrCreate(
            ['form_id' => $form->id, 'user_id' => $user->id],
            [
                'name'         => $user->name,
                'machine_code' => $user->machine_code,
                'start_at'     => Carbon::now(),
            ],
        );

        // Attach computed end_at so the caller can expose it to the client
        $userForm->end_at = Carbon::parse($userForm->start_at)->addMinutes($form->duration);

        return $userForm;
    }

    /**
     * Submit form answers, calculate score, store results.
     * Mirrors FrontControllers\FormController::saveExam().
     *
     * $questions format (same as exam submission):
     * [
     *   ['question_id' => 1, 'question_title' => '...', 'answer_id' => '3'],
     *   ['question_id' => 2, 'question_title' => '...', 'answer_id' => 'Some text answer'],
     * ]
     */
    public function submitForm(User $user, Form $form, array $questions, int $minutesRemaining = 0): UserForm
    {
        $userForm = UserForm::where('user_id', $user->id)
            ->where('form_id', $form->id)
            ->firstOrFail();

        $correctAnswers = 0;

        foreach ($questions as $questionData) {
            $answerValue = $questionData['answer_id'];

            $answerRow = [
                'question_id' => $questionData['question_id'],
                'question'    => $questionData['question_title'],
                'answer_id'   => null,
                'answer'      => null,
                'is_true'     => false,
            ];

            if (is_numeric($answerValue)) {
                $answer = FormQuestionAnswer::find((int) $answerValue);
                if ($answer) {
                    $answerRow['answer_id'] = $answer->id;
                    $answerRow['answer']    = $answer->answer;
                    $answerRow['is_true']   = (bool) $answer->is_true;
                    if ($answer->is_true) {
                        $correctAnswers++;
                    }
                }
            } else {
                // Text answer — treat as correct
                $answerRow['answer'] = $answerValue;
                $answerRow['is_true'] = true;
                $correctAnswers++;
            }

            $userForm->answers()->create($answerRow);
        }

        $totalQuestions = count($questions);
        $userDegree     = $totalQuestions > 0
            ? ($form->full_mark / $totalQuestions) * $correctAnswers
            : 0;

        $duration = $form->duration - $minutesRemaining;

        $userForm->update([
            'mark'     => $userDegree,
            'duration' => $duration > 0 ? $duration : $form->duration,
        ]);

        return $userForm->fresh();
    }

    public function hasSubmitted(int $userId, int $formId): bool
    {
        return UserForm::where('user_id', $userId)
            ->where('form_id', $formId)
            ->whereNotNull('mark')
            ->exists();
    }
}
