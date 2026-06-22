<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\QualificationSkillRequest;
use App\Models\QualificationSkill;
use App\Services\QualificationSkillService;
use Illuminate\Http\Request;

class QualificationSkillController extends Controller
{
    public function __construct(private readonly QualificationSkillService $service)
    {
        $this->middleware('permission:qualification-skills-index')->only(['index', 'show']);
        $this->middleware('permission:qualification-skills-create')->only(['create', 'store']);
        $this->middleware('permission:qualification-skills-edit')->only(['edit', 'update']);
        $this->middleware('permission:qualification-skills-delete')->only(['destroy']);
    }

    /*** Index of the resource.***/
    public function index(Request $request)
    {
        $content = $this->service->list(20, $request->get('search'));
        return view('admin_dashboard.qualification-skills.index', compact('content'));
    }

    /*** Create form of the resource.***/
    public function create()
    {
        return view('admin_dashboard.qualification-skills.create')->with([
            'content' => new QualificationSkill,
        ]);
    }

    /*** Store form of the resource.***/
    public function store(QualificationSkillRequest $request)
    {
        $this->service->create($request->validated());
        toastr()->success(__('text.insertMsg'), ['timeOut' => 8000], 'success');
        return redirect()->route('admin.qualification-skills.index');
    }

    /*** Edit form of the resource.***/
    public function edit(QualificationSkill $qualification_skill)
    {
        return view('admin_dashboard.qualification-skills.edit')->with([
            'content' => $qualification_skill,
        ]);
    }

    /*** Update form of the resource.***/
    public function update(QualificationSkillRequest $request, QualificationSkill $qualification_skill)
    {
        $this->service->update($qualification_skill, $request->validated());
        toastr()->success(__('text.updateMsg'), ['timeOut' => 8000], 'success');
        return redirect()->route('admin.qualification-skills.index');
    }

    /*** Delete form of the resource.***/
    public function destroy(QualificationSkill $qualification_skill)
    {
        $this->service->delete($qualification_skill);
    }
}
