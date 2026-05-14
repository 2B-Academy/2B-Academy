<?php

namespace App\Http\Controllers\AdminControllers;
use App\Http\Controllers\Controller;
use App\Http\Requests\AdminRequest;
use App\Http\Traits\HasFile;
use App\Http\Traits\HelperTrait;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminController extends Controller
{
    use HasFile,HelperTrait;

    public function __construct()
    {
        $this->middleware('permission:admins-index')->only(['index', 'show']);
        $this->middleware('permission:admins-create')->only(['create', 'store']);
        $this->middleware('permission:admins-edit')->only(['edit', 'update']);
        $this->middleware('permission:admins-delete')->only(['destroy']);
    }

    /*** Index of the resource.***/
    public function index()
    {
        $content = Admin::paginate(20);
        return view('admin_dashboard.admins.index',compact('content'));
    }

    /*** Create form of the resource.***/
    public function create()
    {
        $roles = Role::get();
        return view('admin_dashboard.admins.create')->with(['content' => new Admin, 'roles' =>$roles]);
    }

    /*** Store form of the resource.***/
    public function store(AdminRequest $request)
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);
        $admin = Admin::create($data);
        $admin->assignRole($request->role);
        toastr()->success(__('text.insertMsg'), ['timeOut' => 8000], 'success');
        return redirect()->back();
    }


    /*** Edit form of the resource.***/
    public function edit(Admin $admin)
    {
        $roles = Role::get();
        return view('admin_dashboard.admins.edit')->with(['content' => $admin, 'roles' =>$roles]);
    }


    /*** Update form of the resource.***/
    public function update(AdminRequest $request,Admin $admin)
    {
        $data = $request->validated();
        $admin->name = $data['name'];
        $admin->email = $data['email'];
        if ($request->filled('password')) {
            $admin->password = Hash::make($data['password']);
        }
        $admin->save();
        $admin->syncRoles([$request->role]);
        toastr()->success(__('text.updateMsg'), ['timeOut' => 8000], 'success');
        return redirect()->back();
    }

    /*** Delete form of the resource.***/
    public function destroy(Admin $admin)
    {
        $admin->delete();
    }


}
