<?php

namespace App\Http\Controllers\AuthControllers;

use App\Http\Controllers\Controller;
use App\Http\Traits\HelperTrait;
use App\Models\User;
use App\Services\HRSystemService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{
    use HelperTrait;

    public function login()
    {
        if(Auth::check())
        {
            return redirect()->route('front.auth.dashboard');
        }
        return view('front.auth.login');
    }


    public function postLogin(Request $request)
    {
        $request->validate(['email' => 'required', 'password' => 'required']);
        $service = new HRSystemService();
        $result = $service->getAccessToken($request->email, $request->password, true);
        if(!$result)
        {
            Session::flash('error', 'خطأ في البريد الإلكتروني أو كلمة المرور ');
            return redirect()->back();
        }
        $user_from_hr_system = $result->employee;
        $user_db = User::updateOrCreate(['system_id' => $user_from_hr_system->employeeId], [
                            'name' => $user_from_hr_system->name,
                            'email' => $user_from_hr_system->email,
                            'phone' => $user_from_hr_system->phone ?? null,
                            'machine_code' => $user_from_hr_system->machineCode,
                            'department_name' => $user_from_hr_system->departmentName,
                        ]
                    );

        Auth::login($user_db);
        Session::flash('success', 'تم تسجيل الدخول بنجاح');

        $intendedUrl = session()->pull('url.intended');
        if ($intendedUrl && str_contains($intendedUrl, 'exam')) {
            return redirect()->to($intendedUrl);
        }
        return redirect()->route('front.auth.dashboard');
    }

}
