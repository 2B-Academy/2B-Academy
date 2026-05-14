<?php

namespace App\Http\Controllers\AdminControllers;
use App\Http\Controllers\Controller;
use App\Http\Requests\RoleRequest;
use App\Http\Traits\HasFile;
use App\Http\Traits\HelperTrait;
use Spatie\Permission\Models\Role;
use App\Models\Neighborhood;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    use HasFile,HelperTrait;

    public function __construct()
    {
        $this->middleware('permission:roles-index')->only(['index', 'show']);
        $this->middleware('permission:roles-create')->only(['create', 'store']);
        $this->middleware('permission:roles-edit')->only(['edit', 'update']);
        $this->middleware('permission:roles-delete')->only(['destroy']);
    }

    /*** Index of the resource.***/
    public function index()
    {
        $content = Role::paginate(20);
        return view('admin_dashboard.roles.index',compact('content'));
    }

    /*** Create form of the resource.***/
    public function create()
    {
        $pages = $this->admin_pages();
        return view('admin_dashboard.roles.create')->with(['content' => new Role, 'pages' => $pages]);
    }

    /*** Store form of the resource.***/
    public function store(RoleRequest $request)
    {
        $data = $request->validated();
        $role = Role::create($data);
        $role->syncPermissions($request->pages); // pages = array of permission names
        toastr()->success(__('text.insertMsg'), ['timeOut' => 8000], 'success');
        return redirect()->back();
    }


    /*** Edit form of the resource.***/
    public function edit(Role $role)
    {
        $pages = $this->admin_pages();
        return view('admin_dashboard.roles.edit')->with(['content' => $role, 'pages' =>$pages]);
    }


    /*** Update form of the resource.***/
    public function update(RoleRequest $request,Role $role)
    {
        $data = $request->validated();
        $role->name = $data['name'];
        $role->save();
        $role->syncPermissions($request->pages); // array of permission names
        toastr()->success(__('text.updateMsg'), ['timeOut' => 8000], 'success');
        return redirect()->back();
    }

    /*** Delete form of the resource.***/
    public function destroy(Role $role)
    {
        $role->delete();
    }

}
