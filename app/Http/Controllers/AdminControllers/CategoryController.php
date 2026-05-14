<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Http\Traits\HasFile;
use App\Http\Traits\HelperTrait;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    use HasFile, HelperTrait;

    public function __construct()
    {
        $this->middleware('permission:categories-index')->only(['index', 'show']);
        $this->middleware('permission:categories-create')->only(['create', 'store']);
        $this->middleware('permission:categories-edit')->only(['edit', 'update']);
        $this->middleware('permission:categories-delete')->only(['destroy']);
    }


    /*** Index of the resource.***/
    public function index()
    {
        $content = Category::latest()->paginate(20);
        return view('admin_dashboard.categories.index', compact('content'));
    }

    /*** Create form of the resource.***/
    public function create()
    {
        return view('admin_dashboard.categories.create')->with(['content' => new Category]);
    }

    /*** Store form of the resource.***/
    public function store(CategoryRequest $request)
    {
        $data = $request->validated();
        if ($request->hasFile('logo')) {
            $data['logo'] = $this->uploadRequestFile('Category', $request, 'logo');
        }
        $data['active'] = isset($data['active']);
        Category::create($data);
        toastr()->success(__('text.insertMsg'), ['timeOut' => 8000], 'success');
        return redirect()->back();
    }


    /*** Edit form of the resource.***/
    public function edit(Category $category)
    {
        return view('admin_dashboard.categories.edit')->with(['content' => $category]);
    }


    /*** Update form of the resource.***/
    public function update(CategoryRequest $request, Category $category)
    {
        $data = $request->validated();
        if ($request->hasFile('logo')) {
            $data['logo'] = $this->uploadRequestFile('Category', $request, 'logo');
        }
        $data['active'] = isset($data['active']);
        $category->update($data);
        toastr()->success(__('text.updateMsg'), ['timeOut' => 8000], 'success');
        return redirect()->back();
    }

    /*** Delete form of the resource.***/
    public function destroy(Category $category)
    {
        $category->delete();
    }
}