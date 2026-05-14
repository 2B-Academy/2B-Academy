<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\CourseSectionRequest;
use App\Http\Traits\HasFile;
use App\Http\Traits\HelperTrait;
use App\Models\Category;
use App\Models\Course;
use App\Models\CourseSection;
use Illuminate\Http\Request;

class CourseSectionController extends Controller
{
    use HasFile, HelperTrait;

    public function __construct()
    {
        $this->middleware('permission:courses-sections-index')->only(['index', 'store']);
    }


    /*** Index of the resource.***/
    public function index(Course $course)
    {
        $content = $course->sections;
        return view('admin_dashboard.courses.sections.index', compact('content','course'));
    }


    /*** Store form of the resource.***/
    public function store(CourseSectionRequest $request, Course $course)
    {
        $data = $request->validated();
        if(count($request->name) > 0)
        {
            $submittedIds = $request->section_id ?? []; // Might be empty
            $submittedNames = $request->name;

            // 1. Delete removed sections
            $course->sections()->whereNotIn('id', $submittedIds)->delete();

            // 2. Update or create
            foreach ($submittedNames as $index => $name) {
                $id = $submittedIds[$index] ?? null;

                if ($id) {
                    // Update existing
                    $section = $course->sections()->find($id);
                    if ($section) {
                        $section->update(['name' => $name]);
                    }
                } else {
                    // Create new
                    $course->sections()->create(['name' => $name]);
                }
            }
        }
        toastr()->success(__('text.insertMsg'), ['timeOut' => 8000], 'success');
        return redirect()->back();
    }

    public function destroyAll(Course $course)
    {
        $course->sections()->delete();
    }
    
}