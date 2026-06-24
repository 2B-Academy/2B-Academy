<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Requests\AdminLoginRequest;
use App\Http\Traits\HelperTrait;
use App\Http\Traits\TracksLastActive;
use App\Models\User;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    use HelperTrait;
    use TracksLastActive;

    //login page
    public function login_page()
    {
        if(auth()->guard('admin')->user())
        {
            return redirect()->route('admin.dashboard');
        }
        return view('admin_dashboard.login');
    }

    //login
    public function login(AdminLoginRequest $request)
    {
        $data = $request->validated();
        if (Auth::guard('admin')->attempt($data)) {
            $admin = auth()->guard('admin')->user();
            $this->stampLastActive($admin, force: true);
            $this->stampLastActiveByEmail($admin->email ?? ($data['email'] ?? null));
            $this->saveAdminLoginLog($admin);
            toastr()->success(__('text.successLogin'), ['timeOut' => 8000], __('text.good'));
            return redirect()->route('admin.dashboard');
        }
        toastr()->error(__('text.LoginError'), ['timeOut' => 8000], __('text.failed'));
        return redirect()->back();
    }



}


