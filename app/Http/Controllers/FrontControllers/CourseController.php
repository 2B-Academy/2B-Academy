<?php

namespace App\Http\Controllers\FrontControllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContactRequest;
use App\Http\Requests\CourseRatingRequest;
use App\Http\Traits\HelperTrait;
use App\Models\Contact;
use App\Models\Course;
use App\Models\CourseExam;
use App\Models\CourseExamQuestionAnswer;
use App\Models\CourseLecture;
use App\Models\CourseLectureQuestion;
use App\Models\CourseRating;
use App\Models\UserExam;
use App\Models\UserLectureProgress;
use App\Models\UsersCourse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use function PHPUnit\TestFixture\func;

class CourseController extends Controller
{
    use HelperTrait;


    public function courses(Request $request)
    {
        $categories = is_array($request->category) ? $request->category : [];
        $levels = is_array($request->level) ? $request->level : [];
        $courses = Course::with(['category', 'instructors'])->whereHas('category', function ($query) {
                $query->active();
            })
            ->withCount('lectures')
            ->withCount('ratings')
            ->withAvg('ratings', 'rating')
            ->when($categories, function ($query, $categories) {
                $query->whereIn('category_id', $categories);
            })
            ->when($levels, function ($query, $levels) {
                $query->whereIn('level', $levels);
            })
            ->active()->latest()->paginate(15);
        return view('front.courses.courses', compact('courses'));
    }

    public function courseDetails($id, $slug)
    {
        $course = Course::with(['category','sections.lectures','sections.exams', 'resources', 'instructors','ratings.user'])
            ->whereHas('category', function ($query) {
                $query->active();
            })->active()
            ->withCount('ratings')
            ->withCount('users')
            ->withAvg('ratings', 'rating')->findOrFail($id);

        $user_rating = (Auth::check())  ? CourseRating::where(['user_id' => auth()->user()->id,'course_id' => $course->id])->first() : new CourseRating();
        $enrolled = $this->hasCourse($course->id);
        $my_group = $this->myGroup($course->id);
        return view('front.courses.course-details', compact('course', 'user_rating', 'enrolled', 'my_group'));
    }


    public function rating(CourseRatingRequest $request, Course $course)
    {
        $data = $request->validated();
        $user_course_lectures_progress = $this->userCourseProgress($course->id, auth()->id());
        if($user_course_lectures_progress < 70  && $course->course_type != 'offline')
        {
            return $this->errorResponse('لا يمكنك تقييم الدورة التدريبية إلا بعد  مشاهدة 70% من المحاضرات');

        }
        CourseRating::updateOrCreate([
            'user_id' => auth()->user()->id,
            'course_id' => $course->id,
        ],[
            'rating' => $data['rating'],
            'comment' => $data['comment'],
        ]);
        return $this->successResponse('تم التقييم بنجاح');
    }


    //Lecture Page (Video)
    public function lecture(Request $request,$course_id, $lecture_id)
    {
        $course = Course::whereHas('category', function ($query) {
            $query->active();
        })->active()->findOrFail($course_id);
        $lecture = CourseLecture::findOrFail($lecture_id);
        $enrolled = $this->hasCourse($course->id);
        if(!$enrolled)
        {
            Session::flash('error', 'أنت ليس مسجل علي هذه الدورة التدريبية');
            return redirect()->route('front.course.details', [$course->id, $course->slug]);
        }
        $next = CourseLecture::where('course_id', $course->id)
            ->where('id', '>', $lecture->id)
            ->orderBy('id', 'asc')
            ->first();
        $previous = CourseLecture::where('course_id', $course->id)
            ->where('id', '<', $lecture->id)
            ->orderBy('id', 'desc')
            ->first();

        $progress = UserLectureProgress::where('user_id', auth()->id())
            ->where('lecture_id', $lecture->id)
            ->first();

        return view('front.courses.course-lecture', compact('course', 'lecture', 'enrolled', 'next', 'previous','progress'));
    }


    //Exam Page (Exam)
    public function exam($course_id, $exam_id)
    {
        $course = Course::whereHas('category', function ($query) {
            $query->active();
        })->active()->findOrFail($course_id);
        $exam = CourseExam::with(['questions.answers'])->withCount('questions')->findOrFail($exam_id);
        $enrolled = $this->hasCourse($course->id);
        if(!$enrolled)
        {
            Session::flash('error', 'أنت ليس مسجل علي هذه الدورة التدريبية');
            return redirect()->route('front.course.details', [$course->id, $course->slug]);
        }
        $user_course_lectures_progress = $this->userCourseProgress($course->id, auth()->id());

        $already_submitted = UserExam::where(['user_id'=> auth()->user()->id, 'exam_id' => $exam->id])->first();
        return view('front.courses.course-exam', compact('course', 'exam', 'enrolled', 'already_submitted','user_course_lectures_progress'));
    }

    public function addLectureQuestion(Request $request, Course $course, CourseLecture $lecture)
    {
        $request->validate(['question' => 'required']);
        CourseLectureQuestion::create([
            'user_id' => auth()->user()->id,
            'course_id' => $course->id,
            'lecture_id' => $lecture->id,
            'question' => $request->question,
        ]);
        return $this->successResponse('تم إضافة السؤال بنجاح');
    }



    public function submitCourseExam(Request $request, Course $course, CourseExam $exam)
    {
        if(UserExam::where(['user_id'=> auth()->user()->id, 'exam_id' => $exam->id])->exists())
        {
            Session::flash('error', 'لقد قمت بالإختبار من قبل');
            return redirect()->back();
        }
        if(isset($request->questions) && count($request->questions) > 0)
        {
            $correct_answers = 0;
            $user_exam = UserExam::create([
                'user_id'   => auth()->user()->id,
                'course_id' => $course->id,
                'exam_id'   => $exam->id,
            ]);
            foreach ($request->questions as $question)
            {
                $answer = CourseExamQuestionAnswer::find($question['answer_id']) ?? new CourseExamQuestionAnswer();
                $user_exam->answers()->create([
                    'question_id' => $question['question_id'],
                    'question'    => $question['question_title'],
                    'answer_id'   => $question['answer_id'],
                    'answer'      => $answer->answer,
                    'is_correct'  => $answer->is_correct,
                ]);
                if ($answer->is_correct) {
                    $correct_answers++;
                }
            }

            // calculate degree
            $total_questions   = count($request->questions);
            $user_degree       = ($exam->degree / $total_questions) * $correct_answers;

            // determine status
            $status = ($user_degree >= ($exam->degree / 2)) ? 'success' : 'fail';

            // update exam record
            $user_exam->update([
                'user_degree' => $user_degree,
                'status'      => $status
            ]);

            // Issue the first-class certificate on a passing final exam.
            if ($status === 'success') {
                app(\App\Services\CertificateService::class)->issueFromExam($user_exam);
            }

            Session::flash('success', 'تم حفظ إجاباتك بنجاح');
            return redirect()->route('front.auth.my-exams');
        }
        Session::flash('error', 'من فضلك جاوب جميع الأسئلة (إجباري)');
        return redirect()->back();
    }


    public function stream(Request $request, $filename)
    {
        $path = public_path("storage/{$filename}");

        if (!file_exists($path)) {
            abort(404);
        }
        $headers = [
            "Content-Type" => "video/mp4",
            "Accept-Ranges" => "bytes",
        ];

        return response()->file($path, $headers);
    }



    public function progress(Request $request)
    {
        $data = $request->validate([
            'lecture_id' => 'required|exists:course_lectures,id',
            'progress'   => 'required|integer|min:0|max:100',
        ]);
        UserLectureProgress::updateOrCreate(
            [
                'user_id'   => auth()->id(),
                'lecture_id'=> $data['lecture_id']
            ],
            [
                'progress'  => $data['progress'],
                'completed' => $data['progress'] >= 90 // mark as completed if watched 90%+
            ]
        );
        return $this->successResponse('تم الحفظ بنجاح');
    }


}
