<?php

namespace App\Http\Controllers\AdminControllers;

use App\Exports\UsersProgressCoursesExport;
use App\Http\Controllers\Controller;
use App\Http\Traits\HasFile;
use App\Http\Traits\HelperTrait;
use App\Models\Course;
use App\Models\CourseSection;
use App\Models\User;
use App\Models\UserExam;
use App\Models\UserLectureProgress;
use App\Models\UsersCourse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class UserCourseProgressController extends Controller
{
    use HasFile, HelperTrait;

    public function __construct()
    {
        $this->middleware('permission:users-courses-progress-index')->only(['index']);
    }

    /*** Index of the resource.***/
    public function index()
    {
        // هنا عايز اجيب كل المستخدمين اللي معمول ليهم اساين للكورسات واجيب البروجريس لو امتحن او حضر او عمل تقييم

        $selectedCourse = request()->get('course_id');
        $selectedGroup  = request()->get('group_id');
        $selectedUser   = request()->get('user_id');

        $query = User::query()->select([
                'users.id',
                'users.machine_code',
                'users.name',
                'users.department_name',
                'courses.title as course_title',
                'courses.course_type as course_type',
                'courses.for_public as for_public',
                'course_sections.name as group_name',
                'courses.id as course_id',
            ])
            ->join('users_courses', 'users.id', '=', 'users_courses.user_id')
            ->join('courses', 'courses.id', '=', 'users_courses.course_id')
            ->leftJoin('course_sections', 'course_sections.id', '=', 'users_courses.group_id')
            ->when($selectedCourse, function ($q) use ($selectedCourse) {
                $q->where('courses.id', $selectedCourse);
            })
            ->when($selectedGroup, function ($q) use ($selectedGroup) {
                $q->where('users_courses.group_id', $selectedGroup);
            })
            ->when($selectedUser, function ($q) use ($selectedUser) {
                $q->where('users.id', $selectedUser);
            })->with(['exams', 'evaluations']);
        $results = $query->paginate(50)->withQueryString();
        $results->getCollection()->transform(function ($user) {
            $finalExam = $user->exams->first();
            $evaluation = $user->evaluations->first();

            // نسبة التقدم
            if ($finalExam && !is_null($finalExam->user_degree)) {
                $progress = 100;
            } elseif ($evaluation) {
                $progress = 100;
            } else {
                $progress = 0;
            }

            return [
                'machine_code' => $user->machine_code,
                'user'         => $user->name,
                'department_name' => $user->department_name,
                'course'       => $user->course_title,
                'group'        => $user->group_name,
                'user_degree'  => $finalExam ? $finalExam->user_degree : null,
                'total_degree'  => $finalExam ? $finalExam->exam?->degree : null,
                'progress'     => $progress,
                'course_type'   => $user->course_type,
                'course_public'   => $user->for_public,
                'course_id'=> $user->id,
                'user_id'  => $user->id,
            ];
        });
        $allCourses = Course::active()->pluck('title', 'id');

        return view('admin_dashboard.users-courses-progress.index', [
            'content' => $results,
            'allCourses' => $allCourses,
            'selectedCourse' => $selectedCourse
        ]);


    }


    public function export(Request $request)
    {
        $filters = $request->only(['course_id', 'user_id', 'group_id']);
        return Excel::download(new UsersProgressCoursesExport($filters), 'usersProgress.xlsx');
    }

}
