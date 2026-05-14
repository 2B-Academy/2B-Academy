<?php

namespace App\Http\Controllers\AdminControllers;

use App\Exports\FormExport;
use App\Exports\FormQuestionsExport;
use App\Exports\FormTextQuestionsExport;
use App\Exports\FormWrongQuestionsExport;
use App\Exports\UsersProgressCoursesExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\FormQuestionsRequest;
use App\Http\Requests\FormsRequest;
use App\Http\Traits\HasFile;
use App\Http\Traits\HelperTrait;
use App\Models\Form;
use App\Models\FormQuestion;
use App\Models\FormQuestionAnswer;
use App\Models\UserForm;
use App\Models\UserFormAnswers;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class FormController extends Controller
{
    use HasFile, HelperTrait;

    public function __construct()
    {
        $this->middleware('permission:forms-index')->only(['index', 'show']);
        $this->middleware('permission:forms-create')->only(['create', 'store']);
        $this->middleware('permission:forms-edit')->only(['edit', 'update']);
        $this->middleware('permission:forms-delete')->only(['destroy']);
    }


    /*** Index of the resource.***/
    public function index()
    {
        $content = Form::latest()->paginate(20);
        return view('admin_dashboard.forms.index', compact('content'));
    }

    /*** Create form of the resource.***/
    public function create()
    {
        return view('admin_dashboard.forms.create')->with(['content' => new Form()]);
    }

    /*** Store form of the resource.***/
    public function store(FormsRequest $request)
    {
        $data = $request->validated();
        $data['active'] = isset($data['active']);
        Form::create($data);
        toastr()->success(__('text.insertMsg'), ['timeOut' => 8000], 'success');
        return redirect()->route('admin.forms.index');
    }

    /*** Edit form of the resource.***/
    public function show(Form $form)
    {
        return view('admin_dashboard.forms.show')->with(['content' => $form]);
    }


    /*** Edit form of the resource.***/
    public function edit(Form $form)
    {
        return view('admin_dashboard.forms.edit')->with(['content' => $form]);
    }


    /*** Update form of the resource.***/
    public function update(FormQuestionsRequest $request, Form $form)
    {
        $data = $request->validated();

        DB::transaction(function () use ($request, $form) {

            $question = $form->questions()->create([
                'question' => $request->question,
                'type'     => $request->type,
            ]);

            if ($question->type === 'radio') {
                foreach ($request->radio_answer as $index => $answer) {
                    $question->answers()->create([
                        'answer'           => $answer,
                        'is_true'          => ($request->radio_answer_check['is_true'] == $index),
                    ]);
                }
            }

            if ($question->type === 'yes_no') {
                foreach ($request->yes_no_answer as $index => $answer) {
                    $question->answers()->create([
                        'answer'           => $answer,
                        'is_true'          => ($request->yes_no_answer_check['is_true'] == $index),
                    ]);
                }
            }
        });

        toastr()->success(__('text.updateMsg'), ['timeOut' => 8000], 'success');
        return redirect()->back();
    }

    public function destroyQuestion(FormQuestion $question)
    {
        $question->delete();
    }

    /*** Delete form of the resource.***/
    public function destroy(Form $form)
    {
        $form->delete();
    }

    /*** Export form .***/
    public function export(Form $form)
    {
        return Excel::download(new FormExport($form), 'public_exam_report.xlsx');
    }

    public function exportMostQuestions(Form $form)
    {
        return Excel::download(new FormQuestionsExport($form), 'public_exam_most_questions_answered_true_report.xlsx');
    }

    public function exportTextQuestions(Form $form)
    {
        return Excel::download(new FormTextQuestionsExport($form), 'public_exam_text_questions_report.xlsx');
    }


    public function exportWrongQuestions(Form $form)
    {
        $userForms = UserForm::with(['answers' => function ($q) {
                $q->where('is_true', 0);
            }])
            ->whereHas('answers', function ($q) {
                $q->where('is_true', 0);
            })
            ->where('form_id', $form->id)
            ->get();

        return Excel::download(new FormWrongQuestionsExport($userForms), 'public_exam_wrong_answers.xlsx');
    }

}
