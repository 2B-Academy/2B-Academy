<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Services\CourseAssignmentService;
use App\Services\CourseService;
use Illuminate\Http\Request;

class UserCourseAssignmentController extends Controller
{
    public function __construct(
        private readonly CourseAssignmentService $assignmentService,
        private readonly CourseService $courseService,
    ) {
        $this->middleware('permission:users-courses-assignments-index')->only(['index']);
        $this->middleware('permission:users-courses-assignments-delete')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $selectedUser   = $request->user_id;
        $selectedCourse = $request->course_id;

        $content    = $this->assignmentService->paginateAllSubmissions(
            $selectedUser   ? (int) $selectedUser   : null,
            $selectedCourse ? (int) $selectedCourse : null,
            20
        );
        $allCourses = $this->courseService->activePluckedTitles();

        return view('admin_dashboard.users-courses-assignments.index', compact('content', 'allCourses',
            'selectedCourse', 'selectedUser'));
    }

    public function edit(int $usersCoursesAssignment)
    {
        $content = $this->assignmentService->findSubmissionById($usersCoursesAssignment);
        return view('admin_dashboard.users-courses-assignments.edit', compact('content'));
    }

    public function update(Request $request, int $usersCoursesAssignment)
    {
        $request->validate([
            'feedback' => 'required',
            'score'    => 'nullable|numeric|min:0|max:100',
        ]);

        $submission = $this->assignmentService->findSubmissionById($usersCoursesAssignment);
        $this->assignmentService->reviewSubmission($submission, $request->feedback, $request->score);

        toastr()->success(__('text.updateMsg'), ['timeOut' => 8000], 'success');
        return redirect()->back();
    }

    public function destroy(int $id)
    {
        $this->assignmentService->deleteSubmissionById($id);
    }
}
