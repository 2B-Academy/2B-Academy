<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\EvaluationRequest;
use App\Http\Traits\HasFile;
use App\Http\Traits\HelperTrait;
use App\Models\Evaluation;
use App\Models\EvaluationCategory;
use Illuminate\Http\Request;

class EvaluationController extends Controller
{
    use HasFile, HelperTrait;

    public function __construct()
    {
        $this->middleware('permission:evaluations-index')->only(['index', 'show']);
        $this->middleware('permission:evaluations-create')->only(['create', 'store']);
        $this->middleware('permission:evaluations-edit')->only(['edit', 'update']);
        $this->middleware('permission:evaluations-delete')->only(['destroy']);
    }


    /*** Index of the resource.***/
    public function index()
    {
        $content = Evaluation::with('category')->paginate(20);
        return view('admin_dashboard.evaluations.index', compact('content'));
    }

    /*** Create form of the resource.***/
    public function create()
    {
        $categories = EvaluationCategory::get();
        return view('admin_dashboard.evaluations.create')->with(['content' => new Evaluation, 'categories' => $categories]);
    }

    /*** Store form of the resource.***/
    public function store(EvaluationRequest $request)
    {
        $data = $request->validated();
        Evaluation::create($data);
        toastr()->success(__('text.insertMsg'), ['timeOut' => 8000], 'success');
        return redirect()->back();
    }


    /*** Edit form of the resource.***/
    public function edit(Evaluation $evaluation)
    {
        $categories = EvaluationCategory::get();
        return view('admin_dashboard.evaluations.edit')->with(['content' => $evaluation, 'categories' => $categories]);
    }


    /*** Update form of the resource.***/
    public function update(EvaluationRequest $request,Evaluation $evaluation)
    {
        $data = $request->validated();
        $evaluation->update($data);
        toastr()->success(__('text.updateMsg'), ['timeOut' => 8000], 'success');
        return redirect()->back();
    }

    /*** Delete form of the resource.***/
    public function destroy(Evaluation $evaluation)
    {
        $evaluation->delete();
    }


}
