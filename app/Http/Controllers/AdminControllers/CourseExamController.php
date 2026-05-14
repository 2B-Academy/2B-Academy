<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\CourseExamRequest;
use App\Http\Traits\HasFile;
use App\Http\Traits\HelperTrait;
use App\Models\Course;
use App\Models\CourseExam;
use App\Models\CourseExamQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class CourseExamController extends Controller
{
    use HasFile, HelperTrait;

    public function __construct()
    {
        $this->middleware('permission:courses-exams-index')->only(['index', 'show']);
        $this->middleware('permission:courses-exams-create')->only(['create', 'store']);
        $this->middleware('permission:courses-exams-edit')->only(['edit', 'update']);
        $this->middleware('permission:courses-exams-delete')->only(['destroy']);
    }


    /*** Index of the resource.***/
    public function index(Course $course)
    {
        $content = $course->sections()->with('exams')->get();
        return view('admin_dashboard.courses.exams.index', compact('content', 'course'));
    }

    /*** Create form of the resource.***/
    public function create(Course $course)
    {
        return view('admin_dashboard.courses.exams.create')->with(['content' => new CourseExam, 'course' => $course]);
    }

    /*** Store form of the resource.***/
    public function store(CourseExamRequest $request, Course $course)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();
            $data['course_id'] = $course->id;
            $data['is_final'] = isset($data['is_final']);
            $course_exam = CourseExam::create(Arr::except($data, ['questions']));
            foreach ($data['questions'] as $question)
            {
                $_question = CourseExamQuestion::create([
                    'course_exam_id' => $course_exam->id,
                    'question' => $question['title'],
                ]);
                foreach ($question['answers'] as $index => $answer) {
                    if(!is_null($answer))
                    {
                        $_question->answers()->create([
                            'answer' => $answer,
                            'is_correct' => $index == (int) $question['is_correct'],
                        ]);
                    }
                }
            }

            DB::commit();
            toastr()->success(__('text.insertMsg'), ['timeOut' => 8000], 'success');
            return redirect()->back();
        }catch (\Exception $e)
        {
            DB::rollBack();
            toastr()->error($e, ['timeOut' => 8000], 'error');
            return redirect()->back();
        }
    }


    /*** Edit form of the resource.***/
    public function edit(Course $course, CourseExam $exam)
    {
        return view('admin_dashboard.courses.exams.edit')->with(['content' => $exam, 'course' => $course]);
    }


    /*** Update form of the resource.***/
    public function update(CourseExamRequest $request,Course $course, CourseExam $exam)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();
            $data['course_id'] = $course->id;
            $data['is_final'] = isset($data['is_final']);
            $exam->update(Arr::except($data, ['questions']));
            // Delete existing questions and answers (to replace them)
            foreach ($exam->questions as $question) {
                $question->answers()->delete();
                $question->delete();
            }
            foreach ($data['questions'] as $question)
            {
                $_question = CourseExamQuestion::create([
                    'course_exam_id' => $exam->id,
                    'question' => $question['title'],
                ]);
                foreach ($question['answers'] as $index => $answer) {
                    if(!is_null($answer))
                    {
                        $_question->answers()->create([
                            'answer' => $answer,
                            'is_correct' => $index == (int) $question['is_correct'],
                        ]);
                    }
                }
            }

            DB::commit();
            toastr()->success(__('text.updateMsg'), ['timeOut' => 8000], 'success');
            return redirect()->back();
        }catch (\Exception $e)
        {
            DB::rollBack();
            toastr()->error($e, ['timeOut' => 8000], 'error');
            return redirect()->back();
        }
    }

    /*** Delete form of the resource.***/
    public function destroy(Course $course,CourseExam $exam)
    {
        $exam->delete();
    }
}
