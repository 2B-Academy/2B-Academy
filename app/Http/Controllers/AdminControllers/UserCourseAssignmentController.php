<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Http\Traits\HasFile;
use App\Http\Traits\HelperTrait;
use App\Models\Course;
use App\Models\CourseRating;
use App\Models\User;
use App\Models\UserCourseAssignment;
use App\Models\UserLectureProgress;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class UserCourseAssignmentController extends Controller
{
    use HasFile, HelperTrait;

    public function __construct()
    {
        $this->middleware('permission:users-courses-assignments-index')->only(['index']);
        $this->middleware('permission:users-courses-assignments-delete')->only(['destroy']);
    }

    /*** Index of the resource.***/
    public function index(Request $request)
    {
        $selectedUser   = $request->user_id;   // filter user
        $selectedCourse = $request->course_id; // filter course

        $content = UserCourseAssignment::with(['user:id,name', 'assignment.course:id,title'])
            ->when($selectedUser, function ($query,$selectedUser) {
                $query->where('user_id', $selectedUser);
            })->when($selectedCourse, function ($query, $selectedCourse) {
                $query->whereHas('assignment.course', function ($q) use ($selectedCourse) {
                    $q->where('id', $selectedCourse);
                });
            })->latest('created_at')->paginate(20);
        $allCourses = Course::active()->pluck('title', 'id');

        return view('admin_dashboard.users-courses-assignments.index', compact('content', 'allCourses',
        'selectedCourse','selectedUser'));
    }


    public function edit(UserCourseAssignment $usersCoursesAssignment)
    {
        $content = $usersCoursesAssignment->load(['user:id,name', 'assignment.course:id,title']);
        return view('admin_dashboard.users-courses-assignments.edit', compact('content'));
    }


    public function update(Request $request, UserCourseAssignment $usersCoursesAssignment)
    {
        $request->validate([
            'feedback' => 'required',
            'score' => 'nullable|numeric|min:0|max:100',
        ]);
        $usersCoursesAssignment->update([
            'feedback' => $request->feedback,
            'score' => $request->score,
        ]);
        toastr()->success(__('text.updateMsg'), ['timeOut' => 8000], 'success');
        return redirect()->back();
    }


    public function destroy($id)
    {
        UserCourseAssignment::whereId($id)->delete();
    }

}
