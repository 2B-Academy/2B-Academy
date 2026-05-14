<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\InstructorRequest;
use App\Http\Traits\HasFile;
use App\Http\Traits\HelperTrait;
use App\Models\Category;
use App\Models\Instructor;
use Illuminate\Http\Request;

class InstructorController extends Controller
{
    use HasFile, HelperTrait;

    public function __construct()
    {
        $this->middleware('permission:instructors-index')->only(['index', 'show']);
        $this->middleware('permission:instructors-create')->only(['create', 'store']);
        $this->middleware('permission:instructors-edit')->only(['edit', 'update']);
        $this->middleware('permission:instructors-delete')->only(['destroy']);
    }


    /*** Index of the resource.***/
    public function index()
    {
        $content = Instructor::latest()->paginate(20);
        return view('admin_dashboard.instructors.index', compact('content'));
    }

    /*** Create form of the resource.***/
    public function create()
    {
        return view('admin_dashboard.instructors.create')->with(['content' => new Instructor]);
    }

    /*** Store form of the resource.***/
    public function store(InstructorRequest $request)
    {
        $data = $request->validated();
        if ($request->hasFile('image')) {
            $data['image'] = $this->uploadRequestFile('Instructor', $request, 'image');
        }
        Instructor::create($data);
        toastr()->success(__('text.insertMsg'), ['timeOut' => 8000], 'success');
        return redirect()->back();
    }


    /*** Edit form of the resource.***/
    public function edit(Instructor $instructor)
    {
        return view('admin_dashboard.instructors.edit')->with(['content' => $instructor]);
    }


    /*** Update form of the resource.***/
    public function update(InstructorRequest $request, Instructor $instructor)
    {
        $data = $request->validated();
       if ($request->hasFile('image')) {
            $data['image'] = $this->uploadRequestFile('Instructor', $request, 'image');
        }
        $instructor->update($data);
        toastr()->success(__('text.updateMsg'), ['timeOut' => 8000], 'success');
        return redirect()->back();
    }

    /*** Delete form of the resource.***/
    public function destroy(Instructor $instructor)
    {
        $instructor->delete();
    }
}