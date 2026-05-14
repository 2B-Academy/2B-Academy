<?php

namespace App\Http\Controllers\FrontControllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\AttendanceRequest;
use App\Http\Traits\HelperTrait;
use App\Models\Attendance;
use App\Models\Course;
use App\Models\User;
use App\Models\UsersCourse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AttendanceController extends Controller
{
    use HelperTrait;

    public function form()
    {
        return view('front.attendances.form');
    }

    public function getUser(Request $request)
    {
        $user = User::where('machine_code', $request->user_machine_code)->first();
        if (!$user) {
            return $this->errorResponse('الكود الوظيفي غير صحيح');
        }
        $courses = $user->courses()->select('courses.id', 'courses.title')->get();
        $html = '<option value="">اختر الدورة التدريبية</option>';
        foreach ($courses as $course) {
            $html .= '<option value="'.$course->id.'">'.$course->title.'</option>';
        }
        return $this->successResponse('', ['html' => $html]);
    }


    public function store(AttendanceRequest $request)
    {
        $data = $request->validated();
        $user = User::where('machine_code', $data['user_machine_code'])->first();
        $course = Course::with('category')->whereId($data['course_id'])->first();
        return $this->saveAttendance($user, $course);
    }



}
