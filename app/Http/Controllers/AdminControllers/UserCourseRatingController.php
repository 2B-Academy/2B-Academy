<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Http\Traits\HasFile;
use App\Http\Traits\HelperTrait;
use App\Models\Course;
use App\Models\CourseRating;
use App\Models\User;
use App\Models\UserLectureProgress;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class UserCourseRatingController extends Controller
{
    use HasFile, HelperTrait;

    public function __construct()
    {
        $this->middleware('permission:users-courses-rating-index')->only(['index']);
        $this->middleware('permission:users-courses-rating-delete')->only(['destroy']);
    }

    /*** Index of the resource.***/
    public function index(Request $request)
    {
        $selectedUser   = $request->user_id;   // filter user
        $selectedCourse = $request->course_id; // filter course

        $content = CourseRating::with(['user:id,name', 'course:id,title'])
            ->when($selectedUser, function ($query,$selectedUser) {
                $query->where('user_id', $selectedUser);
            })->when($selectedCourse, function ($query,$selectedCourse) {
                $query->where('course_id', $selectedCourse);
            })->latest('created_at')->paginate(20);
        $allCourses = Course::active()->pluck('title', 'id');

        return view('admin_dashboard.users-courses-rating.index', compact('content', 'allCourses',
        'selectedCourse','selectedUser'));
    }

}
