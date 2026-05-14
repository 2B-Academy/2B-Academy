<?php

namespace App\Http\Controllers\AdminControllers;
use App\Http\Controllers\Controller;
use App\Http\Requests\TestimonialRequest;
use App\Http\Traits\HasFile;
use App\Http\Traits\HelperTrait;
use App\Models\Testimonial;
use Illuminate\Http\Request;

class TestimonialController extends Controller
{
    use HasFile,HelperTrait;

    public function __construct()
    {
        $this->middleware('permission:testimonials-index')->only(['index', 'show']);
        $this->middleware('permission:testimonials-create')->only(['create', 'store']);
        $this->middleware('permission:testimonials-edit')->only(['edit', 'update']);
        $this->middleware('permission:testimonials-delete')->only(['destroy']);
    }

    /*** Index of the resource.***/
    public function index()
    {
        $content = Testimonial::latest()->paginate(20);
        return view('admin_dashboard.testimonials.index',compact('content'));
    }

    /*** Create form of the resource.***/
    public function create()
    {
        return view('admin_dashboard.testimonials.create')->with(['content' => new Testimonial]);
    }

    /*** Store form of the resource.***/
    public function store(TestimonialRequest $request)
    {
        $data = $request->validated();
        if ($request->hasFile('image')) {
            $data['image'] = $this->uploadRequestFile('Testimonial', $request, 'image');
        }
        $data['active'] = isset($data['active']);
        Testimonial::create($data);
        toastr()->success(__('text.insertMsg'), ['timeOut' => 8000], 'success');
        return redirect()->back();
    }


    /*** Edit form of the resource.***/
    public function edit(Testimonial $testimonial)
    {
        return view('admin_dashboard.testimonials.edit')->with(['content' => $testimonial]);
    }


    /*** Update form of the resource.***/
    public function update(TestimonialRequest $request,Testimonial $testimonial)
    {
        $data = $request->validated();
        if ($request->hasFile('image')) {
            $data['image'] = $this->uploadRequestFile('Testimonial', $request, 'image');
        }
        $data['active'] = isset($data['active']);
        $testimonial->update($data);
        toastr()->success(__('text.updateMsg'), ['timeOut' => 8000], 'success');
        return redirect()->back();
    }

    /*** Delete form of the resource.***/
    public function destroy(Testimonial $testimonial)
    {
        $testimonial->delete();
    }


}
