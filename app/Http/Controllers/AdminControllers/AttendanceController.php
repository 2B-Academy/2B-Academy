<?php

namespace App\Http\Controllers\AdminControllers;

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

class AttendanceController extends Controller
{
    use HasFile, HelperTrait;

    public function __construct()
    {
        $this->middleware('permission:attendances-index')->only(['index', 'show']);
    }


    /*** Index of the resource.***/
    public function qr()
    {
        $attendance_url = route('front.attendances.form');
        return view('admin_dashboard.attendances.qr', compact('attendance_url'));
    }

    /*** Index of the resource.***/
    public function index(Request $request)
    {
        $attendance_by_category_name = $this->getAttendanceByField($request,'course_category_name');
        $attendance_by_department = $this->getAttendanceByField($request,'user_department');
        $attendance_by_course = $this->getAttendanceByField($request,'course_name');
        $attendance_by_month = $this->getAttendanceByField($request,'month');
        $attendance_user_courses = $this->getAttendanceForUsersCourses($request);
        $attendance_by_users = $this->getAttendanceByField($request, 'employee');

        return view('admin_dashboard.attendances.index', compact(
            'attendance_by_category_name',
            'attendance_by_department',
            'attendance_by_course',
            'attendance_by_month',
            'attendance_user_courses',
            'attendance_by_users',
        ));
    }


    public function getAttendanceByField(Request $request,$field)
    {
        $from = $request->from ?? null;
        $to = $request->to ?? null;
        if ($field == 'month') {
            return $this->attendance_by_months();
        }
        elseif ($field == 'employee') {
            return $this->getAttendancesByEmployees($request);
        }
        $subQuery = DB::table('attendances')
            ->select("$field", 'course_id', 'user_id',
                DB::raw('SUM(attendance_hours) as attendance_hours'));
        if ($from && $to) {
            $subQuery->whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to);
        }
        $subQuery->groupBy($field, 'course_id', 'user_id');

        return DB::table(DB::raw("({$subQuery->toSql()}) as t"))
            ->mergeBindings($subQuery)
            ->select(
                "$field as field",
                DB::raw('SUM(attendance_hours) as total_attendance_hours'))
            ->groupBy("$field")
            ->orderByDesc('total_attendance_hours')
            ->get();
    }

    public function attendance_by_months()
    {
        $months = $this->months();
        $data = DB::table('attendances')
            ->select(
                DB::raw('MONTH(created_at) as field'),
                DB::raw('SUM(attendance_hours) as total_attendance_hours')
            )
            ->whereYear('created_at', date('Y'))
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->orderBy('field')
            ->get();
        return $data->map(function ($item) use ($months) {
            $item->field = $months[$item->field] ?? $item->field;
            return $item;
        });
    }

    public function getAttendancesByEmployees(Request $request)
    {
        $query = DB::table('attendances')
            ->select( 'user_id',
                DB::raw('MAX(user_machine_code) as user_machine_code'),
                DB::raw('MAX(users.name) as name'),
                DB::raw('SUM(attendance_hours) as total_attendance_hours'))
            ->leftJoin('users', 'users.id', '=','attendances.user_id');
        $from = $request->from ?? null;
        $to = $request->to ?? null;
        if ($from && $to) {
            $query->whereDate('attendances.created_at', '>=', $from)->whereDate('attendances.created_at', '<=', $to);
        }
        return $query->groupBy('user_id')->get();
    }


    public function getAttendanceForUsersCourses(Request $request)
    {
        $from = $request->from ?? null;
        $to = $request->to ?? null;
        $sessionSubQuery = DB::table('course_sessions')
            ->select('section_id', DB::raw('COUNT(*) as sessions_count'))
            ->when($from && $to, function ($q) use ($from, $to) {
                $q->whereBetween('session_date', [$from, $to]);
            })->groupBy('section_id');

        $query = DB::table('attendances')
            ->join('users', 'users.id', '=', 'attendances.user_id')
            ->join('course_sections', 'course_sections.id', '=', 'attendances.section_id')
            ->leftJoinSub($sessionSubQuery, 'sessions', function ($join) {
                $join->on('sessions.section_id', '=', 'attendances.section_id');
            })
            ->select(
                'attendances.user_machine_code as employee_code',
                DB::raw('COUNT(attendances.user_machine_code) as user_attendance_count'),
                DB::raw('MAX(attendances.user_department) as user_department'),
                DB::raw('MAX(course_sections.name) as group_name'),
                DB::raw('MAX(users.name) as employee_name'),
                'attendances.course_name',
                DB::raw('MAX(attendances.course_category_name) as course_category_name'),
                DB::raw('COALESCE(MAX(sessions.sessions_count),0) as sessions_count'),
                DB::raw('SUM(attendances.attendance_hours) as attendance_hours'),
                DB::raw('MAX(attendances.course_hours) as total_hours')
            );
        if ($from && $to) {
            $query->whereDate('attendances.created_at', '>=', $from)
                ->whereDate('attendances.created_at', '<=', $to);
        }
        return $query->groupBy('attendances.user_machine_code','course_name', 'attendances.section_id')->get();
    }


    public function export(Request $request, $type)
    {
        switch ($type){
            case 'per_department':
                $query = $this->getAttendanceByField($request,'user_department');
                break;
            case 'per_course':
                $query = $this->getAttendanceByField($request,'course_name');
                break;
            case 'per_month':
                $query = $this->getAttendanceByField($request,'month');
                break;
            case 'per_user':
                $query = $this->getAttendanceForUsersCourses($request);
                break;
            case 'per_employee':
                $query = $this->getAttendanceByField($request, 'employee');
                break;
            default:
                $query = $this->getAttendanceByField($request,'course_category_name');
                break;
        }
        $file_name = ($request->from && $request->to) ?
            'attendance_'.$type.'_report_'.$request->from.'__'.$request->to.'.xlsx' :
            'attendance_'.$type.'_report.xlsx';
        return (new AttendanceReport($query,$type))->download($file_name, \Maatwebsite\Excel\Excel::XLSX);
    }



    public function create(Request $request)
    {
        $show_form = false;
        $selectedCourse = request()->get('course_id');
        $selectedGroup = request()->get('group_id');
        $selectedUser = request()->get('user_id');
        $allCourses = Course::active()->pluck('title', 'id');
        if(!is_null($selectedCourse) && !is_null($selectedGroup))
        {
            $show_form = true;
        }
        $course = Course::select('id', 'title')->find($selectedCourse);
        $section  = CourseSection::withCount('sessions')->find($selectedGroup);
        $group_sessions_count =  ($section) ? (($section->sessions_count > 0 ? $section->sessions_count : 1)) : 0;
        $users  = UsersCourse::with(['user' => function ($q) use ($selectedCourse, $selectedGroup) {
                   $q->select('id','machine_code','name')->withCount(['attendances' => function ($q2) use ($selectedCourse, $selectedGroup) {
                       $q2->where('course_id', $selectedCourse)->where('section_id', $selectedGroup);
                   }]);
                }
           ])
            ->when($selectedUser, function ($query,$selectedUser) {
                $query->where('user_id', $selectedUser);
            })->when($selectedCourse, function ($query, $selectedCourse) {
                $query->where('course_id', $selectedCourse);
            })->when($selectedGroup, function ($query, $selectedGroup) {
                $query->where('group_id', $selectedGroup);
            })->get();

        return view('admin_dashboard.attendances.create', compact(
            'allCourses',
            'selectedCourse',
            'users',
            'course',
            'section',
            'group_sessions_count',
            'show_form'
        ));
    }

    public function store(AttendanceStoreRequest $request)
    {
        $data = $request->validated();
        $user = User::find($data['user_id']);
        $course = Course::with('category')->whereId($data['course_id'])->first();
        if ($data['status']) {
            return $this->saveAttendance($user, $course, true);
        } else {
            $attendance = Attendance::where(['user_id' => $user->id, 'course_id' => $course->id])->latest()->first();
            $attendance->logs()->create([
                'user_id' => auth()->user()->id,
                'employee_code' => $user->machine_code,
                'log' => " تم حذف سيشن للموظف $attendance->user_id والدورة التدريبية $attendance->course_id",
            ]);
            $attendance->delete();
            return $this->successResponse('تم حذف تسجيل الحضور بنجاح');
        }
    }


    public function compareDates(Request $request)
    {
        $user = User::find($request->user_id);
        $section =  CourseSection::withCount('sessions')->find($request->section_id);
        if(!$user && !$section)
            return $this->errorResponse('عفواُ - الموظف غير موجود بالنظام');

        $attendances_dates = Attendance::select(['id', 'created_at'])
            ->where('user_id', $user->id)->where('section_id', $section->id)->get();
        $sessions = $section->sessions;

        $attendances_dates_html = '';
        $sessions_html = '';

        foreach ($attendances_dates as $attendance_date)
        {
            $attendance_at = date('Y-m-d H:i:s', strtotime($attendance_date->created_at));
            $attendances_dates_html .= '<tr><td>'.$attendance_at.'</td></tr>';
        }

        if (count($sessions) > 0)
        {
            foreach ($sessions as $session)
            {
                $session_title = $session->title;
                $session_date = $session->session_date;
                $session_time_from = date('H:i A', strtotime($session->time_from));
                $session_time_to = date('H:i A', strtotime($session->time_to));
                $sessions_html .= '<tr><td>'.$session_title.'</td><td>'.$session_date.'</td><td>'.$session_time_from.'</td><td>'.$session_time_to.'</td></tr>';
            }
        }
        else
        {
            $sessions_html .= '<tr><td>'.$section->name.'</td><td>-</td></tr>';
        }

        return $this->successResponse('', [
            'user' => $user,
            'sessions_count' => count($sessions) > 0 ? count($sessions) : 1,
            'attendances_count' => count($attendances_dates),
            'attendances_dates_html' => $attendances_dates_html,
            'sessions_html' => $sessions_html
        ]);
    }

}
