<?php

namespace App\Http\Controllers\AdminControllers;
use App\Http\Controllers\Controller;
use App\Http\Requests\AboutRequest;
use App\Http\Traits\HasFile;
use App\Models\About;
use Illuminate\Http\Request;

class AboutController extends Controller
{
    use HasFile;

    public function __construct()
    {
        $this->middleware('permission:abouts-edit')->only(['index', 'store']);
    }

    public function index()
    {
        $about = About::first();
        $content = ($about) ?? new About();
        return view('admin_dashboard.abouts.index',compact('content'));
    }

    public function store(AboutRequest $request)
    {
        $data = $request->validated();
        if ($request->hasFile('image')) {
            $data['image'] = $this->uploadRequestFile('About' ,$request, 'image');
        }
        About::updateOrCreate(['id' => $request->id],$data);
        toastr()->success(__('text.insertMsg'), ['timeOut' => 8000], 'success');
        return redirect()->back();
    }

}
