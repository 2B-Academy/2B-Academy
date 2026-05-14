<?php

namespace App\Http\Controllers\AdminControllers;
use App\Http\Controllers\Controller;
use App\Http\Requests\ArticleRequest;
use App\Http\Traits\HasFile;
use App\Http\Traits\HelperTrait;
use App\Models\Article;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    use HasFile,HelperTrait;

    public function __construct()
    {
        $this->middleware('permission:blogs-index')->only(['index', 'show']);
        $this->middleware('permission:blogs-create')->only(['create', 'store']);
        $this->middleware('permission:blogs-edit')->only(['edit', 'update']);
        $this->middleware('permission:blogs-delete')->only(['destroy']);
    }


    /*** Index of the resource.***/
    public function index()
    {
        $content = Article::whereType('blogs')->latest()->paginate(20);
        return view('admin_dashboard.blogs.index',compact('content'));
    }

    /*** Create form of the resource.***/
    public function create()
    {
        return view('admin_dashboard.blogs.create')->with(['content' => new Article]);
    }

    /*** Store form of the resource.***/
    public function store(ArticleRequest $request)
    {
        $data = $request->validated();
        if ($request->hasFile('image')) {
            $data['image'] = $this->uploadRequestFile('Article', $request, 'image');
        }
        $data['type'] = 'blogs';
        $data['is_home'] = isset($data['is_home']);
        $data['active'] = isset($data['active']);
        Article::create($data);
        toastr()->success(__('text.insertMsg'), ['timeOut' => 8000], 'success');
        return redirect()->back();
    }


    /*** Edit form of the resource.***/
    public function edit(Article $blog)
    {
        return view('admin_dashboard.blogs.edit')->with(['content' => $blog]);
    }


    /*** Update form of the resource.***/
    public function update(ArticleRequest $request,Article $blog)
    {
        $data = $request->validated();
        if ($request->hasFile('image')) {
            $data['image'] = $this->uploadRequestFile('Article', $request, 'image');
        }
        $data['type'] = 'blogs';
        $data['is_home'] = isset($data['is_home']);
        $data['active'] = isset($data['active']);
        $blog->update($data);
        toastr()->success(__('text.updateMsg'), ['timeOut' => 8000], 'success');
        return redirect()->back();
    }

    /*** Delete form of the resource.***/
    public function destroy(Article $blog)
    {
        $blog->delete();
    }


}
