<?php

use App\Http\Traits\HelperTrait;
use App\Models\Form;
use App\Models\FormQuestionAnswer;
use App\Models\User;
use App\Models\UserForm;
use App\Models\UserFormAnswers;
use App\Services\HRSystemService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

Route::prefix('test')->group(function (){
    Route::get('certificate', function (){
        $cert = (new class {
            use HelperTrait;
        })->generateCertificate('Digital Marketing', 'محمد سعيد عثمان');
        return view('test.cert',compact('cert'));
    });


    Route::get('/hr', function (){
        $hrService = new HRSystemService();
        return  $hrService->getAllEmployees();
    });

    Route::get('/user-forms', function () {
        return false;
        $forms = Form::whereIn('id', [20, 21])
            ->whereActive(true)
            ->get();

        foreach ($forms as $form) {

            $user_forms = UserForm::where('form_id', $form->id)->get();

            foreach ($user_forms as $user_form) {
                $questions = $form->questions;
                $correct_answers = 0;
                $total_questions = count($questions);
                foreach ($questions as $question) {
                    // إجابة المستخدم لهذا السؤال
                    $user_answer = UserFormAnswers::where('user_form_id', $user_form->id)
                        ->where('question_id', $question->id)
                        ->first();
                    if (!$user_answer) {
                        continue;
                    }
                    $is_correct = $question->answers()
                        ->where('answer', $user_answer->answer)
                        ->where('is_true', true)
                        ->exists();
                    if ($is_correct) {
                        $correct_answers++;
                    }
                }
                $user_degree = ($form->full_mark / $total_questions) * $correct_answers;
                $user_form->update([
                    'mark' => $user_degree,
                ]);
            }
        }

        return 'success';
    });
});
