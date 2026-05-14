<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\CourseRequest;
use App\Http\Traits\HasFile;
use App\Http\Traits\HelperTrait;
use App\Models\Category;
use App\Models\Course;
use App\Models\CourseSection;
use App\Models\Instructor;
use App\Models\User;
use App\Models\UsersCourse;
use App\Services\HRSystemService;
use App\Services\NotificationsApiService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class UserCourseOfflineController extends Controller
{
    use HasFile, HelperTrait;

    public function __construct()
    {
        $this->middleware('permission:users-courses-offline-index')->only(['index', 'show']);
        $this->middleware('permission:users-courses-offline-create')->only(['create', 'store']);
        $this->middleware('permission:users-courses-offline-edit')->only(['edit', 'update']);
        $this->middleware('permission:users-courses-offline-delete')->only(['destroy']);
    }


    /*** Index of the resource.***/
    public function index(Request $request)
    {
        $selectedCourse = $request->course_id; // filter course
        $selectedGroup   = $request->group_id;   // filter group_id
        $content = UsersCourse::with(['user:id,name,machine_code,department_name', 'course:id,title', 'group:id,name'])
            ->whereNotNull('group_id')
            ->when($selectedGroup, function ($query,$selectedGroup) {
                $query->where('group_id', $selectedGroup);
            })->when($selectedCourse, function ($query, $selectedCourse) {
                $query->where('course_id', $selectedCourse);
            })->latest('created_at')->paginate(20);
        $allCourses = Course::where('course_type', 'offline')->active()->pluck('title', 'id');
        return view('admin_dashboard.users-courses-offline.index', compact('content', 'allCourses',
            'selectedCourse'));
    }


    /*** Create form of the resource.***/
    public function create()
    {
        $courses = Course::select(['id', 'title'])->where('course_type', 'offline')->active()->get();
        $selectedUsers = [];
        return view('admin_dashboard.users-courses-offline.create')->with(['content' => new Course,
            'courses' => $courses,
            'selectedUsers' => $selectedUsers]);
    }

    /*** Store form of the resource.***/
    public function store(Request $request)
    {
        $validated = [
            'course_id' => 'required|exists:courses,id',
            'group_id' => 'required|exists:course_sections,id',
            'users_sheet' => 'nullable|mimes:xlsx|max:10000',
        ];
        if(!$request->hasFile('users_sheet'))
        {
            $validated['users'] = 'required|array';
            $validated['users.*'] = 'exists:users,id';
        }
        $request->validate($validated);
        $course = Course::find($request->course_id);
        $course->notification_text = $request->notification_text ?? null;
        $course->save();
        $data = [];
        $users = $request->users;
        if($request->hasFile('users_sheet'))
        {
            $main_file = $request->file('users_sheet');
            $users = $this->getUsersFromSheet($main_file);
        }
        foreach ($users as $user_id) {
            $data[$user_id] = ['group_id' => $request->group_id];
        }
        $course->users()->syncWithoutDetaching($data);

        //Send notifications
        if (env('APP_ENV') == 'production')
        {
            $service = new NotificationsApiService();
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

        toastr()->success(__('text.insertMsg'), ['timeOut' => 8000], 'success');
        return redirect()->back();
    }


    public function destroy($id)
    {
        $user_course_offline = UsersCourse::find($id);
        $user_course_offline->delete();
    }



    public function courseGroups(Request $request)
    {
        $course_id = $request->course_id;
        if(!$course_id)
        {
            return $this->errorResponse();
        }
        $course = Course::with('sections')->find($course_id);
        $html = '<option value="">اختر المجموعة</option>';
        foreach ($course->sections as $section)
        {
            $html .= '<option value="'.$section->id.'">'.$section->name.'</option>';
        }
        return $this->successResponse('success', ['html' => $html]);
    }

}
