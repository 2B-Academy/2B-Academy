<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Http\Traits\HasFile;
use App\Http\Traits\HelperTrait;
use App\Jobs\GetAllEmployeesFromHRSystemJob;
use App\Models\CourseLectureQuestion;
use App\Models\CourseRating;
use App\Models\User;
use App\Models\UserExam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

class UserController extends Controller
{
    use HasFile, HelperTrait;

    public function __construct()
    {
        $this->middleware('permission:users-index')->only(['index', 'show']);
    }


    /*** Index of the resource.***/
    public function index(Request $request)
    {
      //  dispatch(new GetAllEmployeesFromHRSystemJob());
        $content = User::when($request->search, function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', '%' . $search . '%')
                    ->orWhere('email', 'LIKE', '%' . $search . '%')
                    ->orWhere('machine_code', 'LIKE', '%' . $search . '%')
                    ->orWhere('department_name', 'LIKE', '%' . $search . '%');
            });
        })
            ->orderBy('system_id')->paginate(20);
        return view('admin_dashboard.users.index', compact('content'));
    }

    /*** show form of the resource.***/
    public function show(User $user)
    {
        $courses = $user->courses()->with(['category'])->latest()->get()->map(function ($course) use ($user){
            $course->user_progress =  $this->userCourseProgress($course->id, $user->id);
            return $course;
        });
        $ratings = $user->ratings()->with('course:id,title')->latest()->get();
        $questions = $user->lectureQuestions()->with(['course:id,title', 'lecture:id,title'])->latest()->get();
        $exams = $user->exams()->with(['course:id,title,certificate', 'exam:id,title,degree,is_final'])->latest()->get();
        return view('admin_dashboard.users.show')->with([
            'content' => $user,
            'courses' => $courses,
            'ratings' => $ratings,
            'questions' => $questions,
            'exams' => $exams,
        ]);
    }

    /*** sync Employees of the resource.***/
    public function syncEmployees()
    {
        Artisan::call('sync:employees');
        toastr()->success(__('text.updateMsg'), ['timeOut' => 8000], 'success');
        return redirect()->back();
    }


    public function addAnswerLectureQuestion(Request $request, $id)
    {
        $request->validate(['answer' => 'required']);
        $question = CourseLectureQuestion::findOrFail($id);
        $question->answer = $request->answer;
        $question->answered_by = $request->user()->id;
        $question->save();
        return $this->successResponse('تم الرد بنجاح');
    }

    /*** delete user course rating.***/
    public function destroyRating($id)
    {
        CourseRating::whereId($id)->delete();
    }

    /*** delete user course lectureQuestion.***/
    public function destroyLectureQuestion($id)
    {
        CourseLectureQuestion::whereId($id)->delete();
    }

    /*** delete user course exam.***/
    public function destroyUserExam($id)
    {
        UserExam::whereId($id)->delete();
    }


    /*** get all users by ajax select2.***/
    public function getUsers(Request $request)
    {
        $content = User::when($request->q, function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', '%' . $search . '%')
                    ->orWhere('email', 'LIKE', '%' . $search . '%')
                    ->orWhere('machine_code', 'LIKE', '%' . $search . '%')
                    ->orWhere('department_name', 'LIKE', '%' . $search . '%');
            });
        })->orderBy('system_id')->get();
        return response()->json($content);
    }


    public function create()
    {
        return view('admin_dashboard.users.create');
    }

    public function store(UserRequest $request)
    {
        $data = $request->validated();
        $data['system_id'] = rand(1,9999999);
        $data['email'] = $data['system_id'].'@2b.com';
        $code = Str::upper(Str::random(4));
        $data['machine_code'] = $code;
        if(User::where('machine_code', $code)->exists())
        {
            toastr()->error('بعض البيانات مسجلة بالفعل جرب مره أخري', ['timeOut' => 8000], 'error');
            return redirect()->back();
        }
        User::create($data);
        toastr()->success(__('text.insertMsg'), ['timeOut' => 8000], 'success');
        return redirect()->back();
    }


}
