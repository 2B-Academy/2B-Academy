<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\CourseRequest;
use App\Http\Traits\HasFile;
use App\Http\Traits\HelperTrait;
use App\Models\Category;
use App\Models\Course;
use App\Models\Instructor;
use App\Models\User;
use App\Models\UsersCourse;
use App\Services\HRSystemService;
use App\Services\NotificationsApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class UserCourseController extends Controller
{
    use HasFile, HelperTrait;

    public function __construct()
    {
        $this->middleware('permission:users-courses-index')->only(['index', 'show']);
        $this->middleware('permission:users-courses-create')->only(['create', 'store']);
        $this->middleware('permission:users-courses-edit')->only(['edit', 'update']);
        $this->middleware('permission:users-courses-delete')->only(['destroy']);
    }


    /*** Index of the resource.***/
    public function index()
    {
        $content = Course::with('users')->where('course_type', 'online')->where(function ($query) {
            $query->has('users')->orWhere('for_public', true);
        })->latest()->paginate(20);
        return view('admin_dashboard.users-courses.index', compact('content'));
    }

    /*** Create form of the resource.***/
    public function create()
    {
        $courses = Course::select(['id', 'title'])->where('course_type', 'online')->active()->get();
        $selectedUsers = [];
        return view('admin_dashboard.users-courses.create')->with(['content' => new Course,
            'courses' => $courses,
            'selectedUsers' => $selectedUsers]);
    }

    /*** Store form of the resource.***/
    public function store(Request $request)
    {
        $validated = [
            'course_id' => 'required|exists:courses,id',
            'users_sheet' => 'nullable|mimes:xlsx|max:10000',
            'notification_text' => 'nullable',
        ];
        if(!isset($request->for_public) && !$request->hasFile('users_sheet'))
        {
            $validated['users'] = 'required|array';
            $validated['users.*'] = 'exists:users,id';
        }
        $request->validate($validated);
        $course = Course::find($request->course_id);
        $course->for_public = isset($request->for_public);
        $course->notification_text = $request->notification_text ?? null;
        $course->save();
        if(!$course->for_public)
        {
            $users = $request->users;
            if($request->hasFile('users_sheet'))
            {
                $main_file = $request->file('users_sheet');
                $users = $this->getUsersFromSheet($main_file);
            }
            $course->users()->syncWithoutDetaching($users);
        }

        //Send notifications
        if (env('APP_ENV') == 'production')
        {
            $service = new NotificationsApiService();
            if ($course->for_public)
            {
                $title = ' تم إضافة كورس جديد لك '. $course->title;
                $body  = $request->notification_text ?: 'A new course has been added '. $course->title;
                $service->sendNotificationsToAllUsers($title, $body, $title, $body);
            }
            else
            {
                if ($request->hasFile('users_sheet'))
                {
                    $main_file = $request->file('users_sheet');
                    $users_ids = $this->getUsersFromSheet($main_file);
                }
                else
                {
                    $users_ids = $request->users;
                }
                if(is_array($users_ids) && count($users_ids) > 0)
                {
                    $users = [];
                    foreach($users_ids as $user_id)
                    {
                        $user = User::whereId($user_id)->first();
                        $users[] = $user?->machine_code;
                    }
                    $title = ' تم إضافة كورس جديد لك '. $course->title;
                    $body  = $request->notification_text ?: $title;
                    $service->sendNotificationsToSelectedUsers($body, $body, $body, $body, $users);
                }
            }
        }
        toastr()->success(__('text.insertMsg'), ['timeOut' => 8000], 'success');
        return redirect()->back();
    }


    /*** Edit form of the resource.***/
    public function edit($course_id)
    {
        $course = Course::findOrFail($course_id);
        $courses = Course::select(['id', 'title'])->where('course_type', 'online')->active()->get();
        $selectedUsers = $course->users()->select('users.id','name','email','department_name','machine_code')->get();
        return view('admin_dashboard.users-courses.edit')->with(['content' => $course, 'courses' => $courses, 'selectedUsers' =>$selectedUsers]);
    }


    /*** Update form of the resource.***/
    public function update(Request $request, $course_id)
    {
        $validated = [];
        if(!isset($request->for_public))
        {
            $validated['users'] = 'required|array';
            $validated['users.*'] = 'exists:users,id';
        }
        $request->validate($validated);
        $course = Course::findOrFail($course_id);
        $course->for_public = isset($request->for_public);
        $course->notification_text = $request->notification_text ?? null;
        $course->save();
        if(!$course->for_public)
        {
            $course->users()->sync($request->users);
        }
        else
        {
            $course->users()->delete();
        }
        toastr()->success(__('text.updateMsg'), ['timeOut' => 8000], 'success');
        return redirect()->back();
    }
}
