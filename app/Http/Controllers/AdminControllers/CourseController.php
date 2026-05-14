<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\CourseRequest;
use App\Http\Traits\HasFile;
use App\Http\Traits\HelperTrait;
use App\Models\Category;
use App\Models\Course;
use App\Models\Instructor;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    use HasFile, HelperTrait;

    public function __construct()
    {
        $this->middleware('permission:courses-index')->only(['index', 'show']);
        $this->middleware('permission:courses-create')->only(['create', 'store']);
        $this->middleware('permission:courses-edit')->only(['edit', 'update']);
        $this->middleware('permission:courses-delete')->only(['destroy']);
    }


    /*** Index of the resource.***/
    public function index(Request $request)
    {
        $name = $request->name;
        $category_id = $request->category_id;
        $content = Course::latest()->when($name, function ($q) use ($name) {
            $q->where('title', 'LIKE', '%'.$name.'%');
        })->when($category_id, function ($q) use ($category_id) {
                $q->where('category_id', $category_id);
            })->paginate(20);
        $categories = Category::active()->get();
        return view('admin_dashboard.courses.index', compact('content','categories'));
    }

    /*** Create form of the resource.***/
    public function create()
    {
        $categories = Category::active()->get();
        $instructors = Instructor::get();
        $selectedInstructorsIds = [];
        return view('admin_dashboard.courses.create')->with(['content' => new Course, 'categories' => $categories,
        'instructors' =>$instructors,'selectedInstructorsIds' =>$selectedInstructorsIds]);
    }

    /*** Store form of the resource.***/
    public function store(CourseRequest $request)
    {
        $data = $request->validated();
        if($request->hasFile('image')) {
            $data['image'] = $this->uploadRequestFile('Course', $request, 'image');
        }
        $data['active'] = isset($data['active']);
        $data['outside_materials'] = isset($data['outside_materials']);
        $data['is_evaluate'] = isset($data['is_evaluate']);
        $data['allow_attendances'] = isset($data['allow_attendances']);
        $course = Course::create($data);
        $course->instructors()->attach($data['instructors']);
        toastr()->success(__('text.insertMsg'), ['timeOut' => 8000], 'success');
        return redirect()->back();
    }


    /*** Edit form of the resource.***/
    public function edit(Course $course)
    {
        $categories = Category::active()->get();
        $instructors = Instructor::get();
        $selectedInstructorsIds = $course->instructors->pluck('id')->toArray();
        return view('admin_dashboard.courses.edit')->with(['content' => $course, 'categories' => $categories,
        'instructors' =>$instructors,'selectedInstructorsIds' =>$selectedInstructorsIds]);
    }


    /*** Update form of the resource.***/
    public function update(CourseRequest $request, Course $course)
    {
        $data = $request->validated();
        if($request->hasFile('image')) {
            $data['image'] = $this->uploadRequestFile('Course', $request, 'image');
        }
        $data['active'] = isset($data['active']);
        $data['outside_materials'] = isset($data['outside_materials']);
        $data['is_evaluate'] = isset($data['is_evaluate']);
        $data['allow_attendances'] = isset($data['allow_attendances']);
        $course->update($data);
        $course->instructors()->sync($data['instructors']);
        toastr()->success(__('text.updateMsg'), ['timeOut' => 8000], 'success');
        return redirect()->back();
    }

    /*** Delete form of the resource.***/
    public function destroy(Course $course)
    {
        $course->delete();
    }
}
