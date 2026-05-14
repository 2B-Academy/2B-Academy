<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\CourseRequest;
use App\Http\Traits\HasFile;
use App\Http\Traits\HelperTrait;
use App\Models\Course;
use App\Models\CourseLectureQuestion;
use App\Models\CourseRating;
use App\Models\User;
use Illuminate\Http\Request;

class UserLectureQuestionController extends Controller
{
    use HasFile, HelperTrait;

    public function __construct()
    {
        $this->middleware('permission:users-lectures-questions-index')->only(['index']);
        $this->middleware('permission:users-lectures-questions-edit')->only(['edit', 'update']);
        $this->middleware('permission:users-lectures-questions-delete')->only(['destroy']);
    }


    /*** Index of the resource.***/
    public function index(Request $request)
    {
        $selectedUser   = $request->user_id;   // filter user
        $selectedCourse = $request->course_id; // filter course
        $answered = $request->answered;

        $content = CourseLectureQuestion::with(['user:id,name', 'course:id,title', 'lecture:id,title'])
            ->when($selectedUser, function ($query,$selectedUser) {
                $query->where('user_id', $selectedUser);
            })->when($selectedCourse, function ($query,$selectedCourse) {
                $query->where('course_id', $selectedCourse);
            })->when($answered, function ($query,$answered) {
                if($answered == 'yes')
                {
                    $query->whereNotNull('answer');
                }elseif($answered == 'no')
                {
                    $query->whereNull('answer');
                }
            })->latest('created_at')->paginate(20);
        $allCourses = Course::active()->pluck('title', 'id');

        return view('admin_dashboard.users-lectures-questions.index', compact('content', 'allCourses',
            'selectedCourse','selectedUser', 'answered'));
    }

}
