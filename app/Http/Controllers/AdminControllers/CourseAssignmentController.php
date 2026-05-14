<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\CourseAssignmentRequest;
use App\Http\Requests\CourseResourceRequest;
use App\Http\Traits\HasFile;
use App\Http\Traits\HelperTrait;
use App\Models\Course;
use Illuminate\Http\Request;

class CourseAssignmentController extends Controller
{
    use HasFile, HelperTrait;

    public function __construct()
    {
        $this->middleware('permission:courses-assignments-index')->only(['index', 'store']);
    }


    /*** Index of the resource.***/
    public function index(Course $course)
    {
        $content = $course->assignments;
        return view('admin_dashboard.courses.assignments.index', compact('content','course'));
    }


    /*** Store form of the resource.***/
    public function store(CourseAssignmentRequest $request, Course $course)
    {
        $data = $request->validated();
        if(count($request->title) > 0)
        {
            $submittedIds = $request->assignment_id ?? []; // Might be empty
            $submittedNames = $request->title;
            $submittedFiles = $request->file('file'); // All uploaded files as array

            // 1. Delete removed sections
            $course->assignments()->whereNotIn('id', $submittedIds)->delete();

            // 2. Update or create
            foreach ($submittedNames as $index => $name) {
                $id = $submittedIds[$index] ?? null;
                $filePath = null;
                // Check if a file exists for this index
                if (isset($submittedFiles[$index])) {
                    // Upload individual file
                    $filePath = $this->uploadRequestFile('CourseAssignment', $request, null, $submittedFiles[$index]);
                }

                if ($id) {
                    // Update existing
                    $resource = $course->assignments()->find($id);
                    if ($resource) {
                        $resource->update(['title' => $name, 'file' => $filePath ?? $resource->file]);
                    }
                } else {
                    // Create new
                    $course->assignments()->create(['title' => $name, 'file' => $filePath ?? '']);
                }
            }
        }
        toastr()->success(__('text.insertMsg'), ['timeOut' => 8000], 'success');
        return redirect()->back();
    }


    //Destroy All
    public function destroyAll(Course $course)
    {
        $course->assignments()->delete();
    }

}
