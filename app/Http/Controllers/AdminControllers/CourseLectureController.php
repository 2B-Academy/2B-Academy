<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\CourseLectureRequest;
use App\Http\Traits\HasFile;
use App\Http\Traits\HelperTrait;
use App\Models\Category;
use App\Models\Course;
use App\Models\CourseLecture;
use Illuminate\Http\Request;

class CourseLectureController extends Controller
{
    use HasFile, HelperTrait;

    public function __construct()
    {
        $this->middleware('permission:courses-lectures-index')->only(['index', 'show']);
        $this->middleware('permission:courses-lectures-create')->only(['create', 'store']);
        $this->middleware('permission:courses-lectures-edit')->only(['edit', 'update']);
        $this->middleware('permission:courses-lectures-delete')->only(['destroy']);
    }


    /*** Index of the resource.***/
    public function index(Course $course)
    {
        $content = $course->sections()->with('lectures')->get();
        return view('admin_dashboard.courses.lectures.index', compact('content', 'course'));
    }

    /*** Create form of the resource.***/
    public function create(Course $course)
    {
        return view('admin_dashboard.courses.lectures.create')->with(['content' => new CourseLecture, 'course' => $course]);
    }

    /*** Store form of the resource.***/
    public function store(CourseLectureRequest $request, Course $course)
    {
        $data = $request->validated();
        $course->lectures()->create($data);
        toastr()->success(__('text.insertMsg'), ['timeOut' => 8000], 'success');
        return redirect()->back();
    }


    /*** Edit form of the resource.***/
    public function edit(Course $course, CourseLecture $lecture)
    {
        return view('admin_dashboard.courses.lectures.edit')->with(['content' => $lecture, 'course' => $course]);
    }


    /*** Update form of the resource.***/
    public function update(CourseLectureRequest $request,Course $course, CourseLecture $lecture)
    {
        $data = $request->validated();
        $course->lectures()->whereId($lecture->id)->update($data);
        toastr()->success(__('text.updateMsg'), ['timeOut' => 8000], 'success');
        return redirect()->back();
    }

    /*** Delete form of the resource.***/
    public function destroy(Course $course,CourseLecture $lecture)
    {
        $lecture->delete();
    }
}
