<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\CourseResourceRequest;
use App\Http\Traits\HasFile;
use App\Http\Traits\HelperTrait;
use App\Models\Category;
use App\Models\Course;
use App\Models\CourseResource;
use Illuminate\Http\Request;

class CourseResourceController extends Controller
{
    use HasFile, HelperTrait;

    public function __construct()
    {
        $this->middleware('permission:courses-resources-index')->only(['index', 'store']);
    }


    /*** Index of the resource.***/
    public function index(Course $course)
    {
        $content = $course->resources;
        return view('admin_dashboard.courses.resources.index', compact('content','course'));
    }


    /*** Store form of the resource.***/
    public function store(CourseResourceRequest $request, Course $course)
    {
        $data = $request->validated();
        if(count($request->title) > 0)
        {
            $submittedIds = $request->resource_id ?? []; // Might be empty
            $submittedNames = $request->title;
            $submittedLinks = $request->link;
            $submittedFiles = $request->file('file'); // All uploaded files as array

            // 1. Delete removed sections
            $course->resources()->whereNotIn('id', $submittedIds)->delete();

            // 2. Update or create
            foreach ($submittedNames as $index => $name) {
                $id = $submittedIds[$index] ?? null;
                $link = $submittedLinks[$index] ?? null;
                $filePath = null;
                // Check if a file exists for this index
                if (isset($submittedFiles[$index])) {
                    // Upload individual file
                    $filePath = $this->uploadRequestFile('CourseResource', $request, null, $submittedFiles[$index]);
                }

                if ($id) {
                    // Update existing
                    $resource = $course->resources()->find($id);
                    if ($resource) {
                        $resource->update(['title' => $name, 'link' => $link, 'file' => $filePath ?? $resource->file]);
                    }
                } else {
                    // Create new
                    $course->resources()->create(['title' => $name, 'link' => $link, 'file' => $filePath ?? '']);
                }
            }
        }
        toastr()->success(__('text.insertMsg'), ['timeOut' => 8000], 'success');
        return redirect()->back();
    }


    //Destroy All
    public function destroyAll(Course $course)
    {
        $course->resources()->delete();
    }
    
}