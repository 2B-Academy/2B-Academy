<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Http\Traits\HasFile;
use App\Http\Traits\HelperTrait;
use App\Models\EvaluationCategory;
use Illuminate\Http\Request;

class EvaluationCategoryController extends Controller
{
    use HasFile, HelperTrait;

    public function __construct()
    {
        $this->middleware('permission:evaluation-categories-index')->only(['index', 'show']);
        $this->middleware('permission:evaluation-categories-create')->only(['create', 'store']);
        $this->middleware('permission:evaluation-categories-edit')->only(['edit', 'update']);
        $this->middleware('permission:evaluation-categories-delete')->only(['destroy']);
    }


    /*** Index of the resource.***/
    public function index()
    {
        $content = EvaluationCategory::paginate(20);
        return view('admin_dashboard.evaluation-categories.index', compact('content'));
    }

    /*** Create form of the resource.***/
    public function create()
    {
        return view('admin_dashboard.evaluation-categories.create')->with(['content' => new EvaluationCategory]);
    }

    /*** Store form of the resource.***/
    public function store(Request $request)
    {
        $request->validate(['name' => 'required']);
        EvaluationCategory::create(['name' => $request->name]);
        toastr()->success(__('text.insertMsg'), ['timeOut' => 8000], 'success');
        return redirect()->back();
    }


    /*** Edit form of the resource.***/
    public function edit(EvaluationCategory $evaluationCategory)
    {
        return view('admin_dashboard.evaluation-categories.edit')->with(['content' => $evaluationCategory]);
    }


    /*** Update form of the resource.***/
    public function update(Request $request,EvaluationCategory $evaluationCategory)
    {
        $request->validate(['name' => 'required']);
        $evaluationCategory->update(['name' => $request->name]);
        toastr()->success(__('text.updateMsg'), ['timeOut' => 8000], 'success');
        return redirect()->back();
    }

    /*** Delete form of the resource.***/
    public function destroy(EvaluationCategory $evaluationCategory)
    {
        $evaluationCategory->delete();
    }


}
