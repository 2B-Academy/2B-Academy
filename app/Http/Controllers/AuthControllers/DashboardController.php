<?php

namespace App\Http\Controllers\AuthControllers;

use App\Http\Controllers\Controller;
use App\Http\Traits\HasFile;
use App\Http\Traits\HelperTrait;
use App\Models\Attendance;
use App\Models\Course;
use App\Models\CourseAssignment;
use App\Models\CourseLectureQuestion;
use App\Models\CourseRating;
use App\Models\CourseSection;
use App\Models\CourseSession;
use App\Models\User;
use App\Models\UserCourseAssignment;
use App\Models\UserCourseEvaluation;
use App\Models\UserExam;
use App\Models\UsersCourse;
use App\Services\HRSystemService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class DashboardController extends Controller
{
    use HelperTrait,HasFile;


    public function getUserStatistics()
    {
        $user_id = auth()->user()->id;

        $courses = Course::where('for_public', true)
            ->orWhereIn('id', function ($query) use ($user_id) {
                $query->select('course_id')
                    ->from('users_courses')
                    ->where('user_id', $user_id);
            })
            ->count();
        $exams = UserExam::where('user_id', $user_id)->count();
        $questions = CourseLectureQuestion::where('user_id', $user_id)->count();
        $certificates = count($this->userCertificates());
        $ratings = CourseRating::where('user_id', $user_id)->count();
        $year_hours = Attendance::where('user_id', $user_id)->whereYear('created_at', date('Y'))->sum('attendance_hours');
        return [
            'courses' => $courses,
            'exams' => $exams,
            'questions' => $questions,
            'certificates' => $certificates,
            'ratings' => $ratings,
            'year_hours' => $year_hours,
        ];
    }

    public function dashboard()
    {
        $stats = $this->getUserStatistics();
        return view('front.auth.dashboard', compact('stats'));
    }

    public function myCourses()
    {
        $userId = auth()->user()->id;
        $courses = Course::where('for_public', true)
            ->orWhereIn('id', function ($query) {
                $query->select('course_id')
                    ->from('users_courses')
                    ->where('user_id', auth()->id());
            })
            ->with([
                'category',
                'usersCourses' => function ($q) use ($userId) {
                    $q->where('user_id', $userId);
                }
            ])
            ->withCount([
                'attendances as user_attendances_count' => function ($q) use ($userId) {
                    $q->where('user_id', $userId);
                }
            ])
            ->latest()
            ->get();
        return view('front.auth.myCourses', compact( 'courses'));
    }

    public function myRatings()
    {
        $ratings = auth()->user()->ratings()->with('course:id,title')->latest()->get();
        return view('front.auth.myRatings', compact( 'ratings'));
    }


    public function myLecturesQuestions()
    {
        $questions = auth()->user()->lectureQuestions()->with(['course:id,title', 'lecture:id,title'])->latest()->get();
        return view('front.auth.myLecturesQuestions', compact( 'questions'));
    }

    public function myExams()
    {
        $exams = auth()->user()->exams()->with(['course:id,title,certificate', 'exam:id,title,degree,is_final'])->latest()->get();
        return view('front.auth.myExams', compact( 'exams'));
    }

    public function myExamAnswers(UserExam $exam)
    {
        $exam_answers = auth()->user()->exams()->where('user_exams.id', $exam->id)
           ->with(['course:id,title,certificate', 'exam:id,title,degree,is_final', 'answers'])->first();
        if(!$exam_answers)
        {
             abort(404);
        }
        return view('front.auth.myExamAnswers', compact( 'exam_answers'));
    }


    public function myAssignments()
    {
        $courses = Course::whereHas('assignments')
            ->where(function ($query) {
                $query->where('for_public', true)
                    ->orWhereIn('id', function ($subQuery) {
                        $subQuery->select('course_id')
                            ->from('users_courses')
                            ->where('user_id', auth()->id());
                    });
            })
            ->with(['assignments'])
            ->latest('courses.created_at')
            ->get();
        $assignments = [];
        foreach ($courses as $course)
        {
            foreach ($course->assignments as $assignment)
            {
                $user_file = UserCourseAssignment::where('user_id', auth()->user()->id)->where('course_assignment_id', $assignment->id)
                ->first();
                $assignments[] = [
                    'assignment_id' => $assignment->id,
                    'course' => $course->title,
                    'title' => $assignment->title,
                    'file' => $assignment->getFileUrl($assignment->file),
                    'user_file' => ($user_file) ? $user_file->getFileUrl($user_file->user_file) : null,
                    'feedback' => ($user_file) ? $user_file->feedback : null,
                    'score' => ($user_file) ? $user_file->score : null,
                ];
            }
        }
        return view('front.auth.myAssignments', compact( 'assignments'));
    }

    public function uploadAssignment(Request $request , $id)
    {
        $request->validate([
            'user_file' => 'required|mimes:pdf,xls,xlsx,csv,doc,docx|max:10000',
        ]);
        $course_assignment = CourseAssignment::find($id);
        if(!$course_assignment)
            abort(404);
        $data = [
            'course_assignment_id' => $course_assignment->id ,
            'user_id' => auth()->user()->id ,
        ];
        if ($request->hasFile('user_file')) {
            $data['user_file'] = $this->uploadRequestFile('UserCourseAssignment', $request, 'user_file');
        }
        if(UserCourseAssignment::where(['user_id' => auth()->user()->id, 'course_assignment_id' => $course_assignment->id])->exists())
        {
            Session::flash('error', 'لقد قمت برفع الملف من قبل');
            return redirect()->back();
        }
        UserCourseAssignment::create($data);
        Session::flash('success', 'تم الحفظ بنجاح');
        return redirect()->back();
    }

    public function myCertificates()
    {
        $certificates = $this->userCertificates();
        return view('front.auth.myCertificates', compact('certificates'));
    }

    public function myCertificate(Course $course)
    {
        $certificate = $this->userCertificate($course);
        if (!$certificate)
            abort(404);
        $course_title = $certificate->course ? ($certificate->course->title_for_certificate ?: $certificate->course->title) : '';
        $user_certificate = $this->generateCertificate($course_title , request('name') ?? auth()->user()->name);
        return view('front.auth.myCertificate', compact( 'certificate', 'user_certificate'));
    }

    public function attendance(Course $course)
    {
        $user = auth()->user();
        return $this->saveAttendance($user, $course);
    }



    public function logout()
    {
       auth()->logout();
       return redirect()->route('front.auth.login');
    }
}
