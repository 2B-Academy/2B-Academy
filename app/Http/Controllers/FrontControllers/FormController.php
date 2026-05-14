<?php

namespace App\Http\Controllers\FrontControllers;

use App\Http\Controllers\Controller;
use App\Http\Traits\HelperTrait;
use App\Models\Form;
use App\Models\FormQuestionAnswer;
use App\Models\UserForm;
use App\Models\UserFormAnswers;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class FormController extends Controller
{
    use HelperTrait;

    public function index($form_uuid)
    {
        $user = auth()->user();
        $form = Form::whereUuid($form_uuid)->whereActive(true)->with('questions.answers')->withCount('questions')->first();
        if (!$form)
            abort(404);

        $user_form = UserForm::where(['form_id' => $form->id, 'user_id' => $user->id])->first();
        if (!$user_form)
        {
            $user_form = UserForm::create([
                'form_id' => $form->id,
                'user_id' => $user->id,
                'name' => $user->name,
                'machine_code' => $user->machine_code,
                'start_at' => Carbon::now()
            ]);
        }
        $user_form->end_at = Carbon::parse($user_form->start_at)->addMinutes($form->duration);
        return  view('front.forms.start', compact('form','user_form'));
    }


    public function saveExam(Request $request, $form_uuid)
    {
        $user = auth()->user();
        $form = Form::whereUuid($form_uuid)->whereActive(true)->first();
        if (!$form)
        {
            Session::flash('error', 'عفواً - الأختبار غير متوفر الآن');
            return redirect()->back();
        }
        $user_form = UserForm::where('user_id', $user->id)->where('form_id', $form->id)->first();
        if (!$user_form)
        {
            Session::flash('error', 'عفواً - الأختبار غير متوفر الآن');
            return redirect()->back();
        }
        if(isset($request->questions))
        {
            $correct_answers = 0;
            foreach ($request->questions as $question) {
                $answer_value = $question['answer_id'];
                $answer_data = [
                    'question_id' => $question['question_id'],
                    'question'    => $question['question_title'],
                    'answer_id'   => null,
                    'answer'      => null,
                    'is_true'     => false,
                ];
                if (is_numeric($answer_value)) {
                    $answer = FormQuestionAnswer::find((int) $answer_value);
                    if ($answer) {
                        $answer_data['answer_id'] = $answer->id;
                        $answer_data['answer']    = $answer->answer;
                        $answer_data['is_true']   = (bool) $answer->is_true;
                        if ($answer->is_true) {
                            $correct_answers++;
                        }
                    }
                }
                else {
                    $answer_data['answer']  = $answer_value;
                    $answer_data['is_true'] = true;
                    $correct_answers++;

                }
                $user_form->answers()->create($answer_data);
            }

            // calculate degree
            $total_questions   = count($request->questions);
            $user_degree       = ($form->full_mark / $total_questions) * $correct_answers;

            // determine status
            $duration = $form->duration - $request->minutes_remaining;

            // update exam record
            $user_form->update([
                'mark'      => $user_degree,
                'duration'  => $duration > 0 ? $duration : $form->duration
            ]);

            Session::flash('success', 'تم انتهاء الإختبار بنجاح');
            return redirect()->back();
        }
        $user_form->update([
            'mark'      => 0,
            'duration'  => $form->duration
        ]);
        Session::flash('success', 'تم انتهاء الإختبار');
        return redirect()->back();
    }


}
