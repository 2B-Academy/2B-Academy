<?php

namespace App\Http\Controllers\AuthControllers;

use App\Http\Controllers\Controller;
use App\Http\Traits\HasFile;
use App\Http\Traits\HelperTrait;
use App\Models\Course;
use App\Models\CourseAssignment;
use App\Models\CourseLectureQuestion;
use App\Models\CourseRating;
use App\Models\Evaluation;
use App\Models\EvaluationCategory;
use App\Models\User;
use App\Models\UserCourseAssignment;
use App\Models\UserCourseEvaluation;
use App\Models\UserExam;
use App\Models\UsersCourse;
use App\Services\HRSystemService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class EvaluationController extends Controller
{
    use HelperTrait,HasFile;

    public function index(Course $course)
    {
        if (!$course->is_evaluate)
            abort(404);

        $evaluation_categories = EvaluationCategory::with('evaluations')->get();
        $already_evaluated = UserCourseEvaluation::where('user_id', auth()->id())->where('course_id', $course->id)->exists();
        return view('front.auth.evaluation', compact('course', 'evaluation_categories', 'already_evaluated'));
    }


    public function store(Request $request, Course $course)
    {
        if (!$course->is_evaluate) {
            return response()->json([
                'status' => false,
                'message' => 'هذا الكورس غير متاح للتقييم'
            ], 403);
        }
        $already_evaluated = UserCourseEvaluation::where('user_id', auth()->id())
            ->where('course_id', $course->id)
            ->exists();
        if ($already_evaluated) {
            return response()->json([
                'status' => false,
                'message' => 'لقد قمت بتقييم هذا الكورس من قبل'
            ], 422);
        }

        $rules = [
            'instructor_id' => 'required|exists:instructors,id',
            'question'      => 'required|array|min:1',
            'question.*'    => 'required',
        ];
        $messages = [
            'instructor_id.required' => 'من فضلك اختر اسم المحاضر',
            'instructor_id.exists'   => 'المحاضر غير موجود',
            'question.required' => 'يجب الإجابة على جميع الأسئلة',
            'question.*.required' => 'يجب الإجابة على هذا السؤال',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // =======================// الحفظ// =======================
        $user = auth()->user();
        $firstRow = null;
        foreach ($request->question as $evaluation_id => $answer) {
            $evaluation = Evaluation::with('category')->find($evaluation_id);
            $row = UserCourseEvaluation::create([
                'user_id' => $user->id,
                'user_machine_code' => $user->machine_code,
                'user_department' => $user->department_name,
                'course_id' => $course->id,
                'course_name' => $course->title,
                'instructor_id' => $request->instructor_id,
                'instructor_name' => optional($course->instructors()->find($request->instructor_id))->name,
                'evaluation_category_id' => optional($evaluation->category)->id,
                'evaluation_category_name' => optional($evaluation->category)->name,
                'evaluation_id' => $evaluation->id,
                'evaluation_title' => $evaluation->title,
                'evaluation_type' => match ($evaluation->type) {
                    'five' => 5,
                    'ten'  => 10,
                    default => 0,
                },
                'answer' => $answer,
            ]);

            $firstRow ??= $row;
        }

        // Completing an evaluation-based course earns its certificate.
        if ($firstRow !== null) {
            $firstRow->setRelation('course', $course);
            $firstRow->setRelation('user', $user);
            app(\App\Services\CertificateService::class)->issueFromEvaluation($firstRow);
        }

        return response()->json([
            'status' => true,
            'message' => 'تم حفظ التقييم بنجاح'
        ]);
    }

}
