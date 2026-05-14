<?php

namespace App\Http\Controllers\AdminControllers;

use App\Exports\AbsenceReport;
use App\Exports\AttendanceReport;
use App\Http\Controllers\Controller;
use App\Http\Requests\AttendanceStoreRequest;
use App\Http\Traits\HasFile;
use App\Http\Traits\HelperTrait;
use App\Models\Attendance;
use App\Models\Category;
use App\Models\Course;
use App\Models\CourseSection;
use App\Models\User;
use App\Models\UsersCourse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class AbsenceController extends Controller
{
    use HasFile, HelperTrait;

    public function __construct()
    {
        $this->middleware('permission:attendances-index')->only(['index', 'show']);
    }



    /*** Index of the resource.***/
    public function index(Request $request)
    {
        $absent_users_function = $this->getAbsencesForUsersCourses($request);
        $absent_users = collect();
        $course = new Course();
        $group = new CourseSection();
        if($absent_users_function)
        {
            $course = $absent_users_function['course'];
            $group = $absent_users_function['group'];
            $absent_users = $absent_users_function['absent_users'];
        }
        $allCourses = Course::active()->pluck('title', 'id');
        return view('admin_dashboard.absences.index', compact('allCourses', 'absent_users', 'course','group'));
    }

    public function getAbsencesForUsersCourses(Request $request)
    {
        $course = Course::with('category')->find($request->course_id);
        $group  = CourseSection::find($request->group_id);
        if (!$course && !$group)
        {
            return false;
        }
        $users_of_course_group = UsersCourse::where('course_id', $course->id)->where('group_id', $group->id)
            ->distinct()->pluck('user_id');
        $attendances = Attendance::where('course_id', $course->id)->where('section_id', $group->id)->distinct()->pluck('user_id');
        $diff_absent_user_ids = $users_of_course_group->diff($attendances);
        $absent_users = User::whereIn('id', $diff_absent_user_ids)->get();
        return [
            'course' => $course,
            'group' => $group,
            'absent_users' => $absent_users,
        ];
    }

    public function export(Request $request)
    {
        $query = $this->getAbsencesForUsersCourses($request);
        $course = $query['course'];
        $group = $query['group'];
        $absent_users = $query['absent_users'];
        $file_name ='absences_'.$course->title.'_'.$group->name.'_report.xlsx' ;
        return (new AbsenceReport($absent_users, $course, $group))->download($file_name, \Maatwebsite\Excel\Excel::XLSX);
    }


}
