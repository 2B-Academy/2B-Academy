<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\CourseSessionRequest;
use App\Http\Traits\HasFile;
use App\Http\Traits\HelperTrait;
use App\Models\Course;
use App\Models\CourseSession;
use Illuminate\Http\Request;

class CourseSessionController extends Controller
{
    use HasFile, HelperTrait;

    public function __construct()
    {
        $this->middleware('permission:courses-sessions-index')->only(['index', 'show']);
        $this->middleware('permission:courses-sessions-create')->only(['create', 'store']);
        $this->middleware('permission:courses-sessions-edit')->only(['edit', 'update']);
        $this->middleware('permission:courses-sessions-delete')->only(['destroy']);
    }


    /*** Index of the resource.***/
    public function index(Course $course)
    {
        $content = $course->sections()->with('sessions')->get();
        return view('admin_dashboard.courses.sessions.index', compact('content', 'course'));
    }

    /*** Create form of the resource.***/
    public function create(Course $course)
    {
        return view('admin_dashboard.courses.sessions.create')->with(['content' => new CourseSession(), 'course' => $course]);
    }

    /*** Store form of the resource.***/
    public function store(CourseSessionRequest $request, Course $course)
    {
        $data = $request->validated();
        if(count($data['title']) > 0)
        {
            foreach ($data['title'] as $index => $name) {
                $course->sessions()->create([
                    'section_id' => $data['section_id'],
                    'time_from' => $data['time_from'],
                    'time_to' => $data['time_to'],
                    'location' => $data['location'],
                    'title' => $name,
                    'session_date' => $data['session_date'][$index],
                ]);
            }
        }
        toastr()->success(__('text.insertMsg'), ['timeOut' => 8000], 'success');
        return redirect()->back();
    }


    /*** Edit form of the resource.***/
    public function edit(Course $course, CourseSession $session)
    {
        return view('admin_dashboard.courses.sessions.edit')->with(['content' => $session, 'course' => $course]);
    }


    /*** Update form of the resource.***/
    public function update(CourseSessionRequest $request,Course $course, CourseSession $session)
    {
        $data = $request->validated();
        $course->sessions()->whereId($session->id)->update($data);
        toastr()->success(__('text.updateMsg'), ['timeOut' => 8000], 'success');
        return redirect()->back();
    }

    /*** Delete form of the resource.***/
    public function destroy(Course $course,CourseSession $session)
    {
        $session->delete();
    }
}
