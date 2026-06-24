<?php

namespace App\Http\Controllers\AuthControllers;

use App\Http\Controllers\Controller;
use App\Http\Traits\HelperTrait;
use App\Http\Traits\TracksLastActive;
use App\Models\User;
use App\Services\HRSystemService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{
    use HelperTrait;
    use TracksLastActive;

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

        // The "email" field may be an email, a machine code (e.g. 2394) or a
        // system id — resolve it to the matching account so any of them work.
        $localUser = $this->resolveLocalUser($request->email);

        // 1) Local-first: honour the password set in the admin dashboard so
        //    accounts created/edited there can sign in without an HR record.
        $localHash = $localUser?->getAttribute('password');
        if ($localUser && !empty($localHash) && Hash::check($request->password, $localHash)) {
            return $this->completeLogin($localUser, $request);
        }

        // 2) Fall back to the HR system for real employees. HR expects the
        //    real email, so use the resolved account's email when available.
        $hrEmail = $localUser->email ?? $request->email;
        $service = new HRSystemService();
        $result = $service->getAccessToken($hrEmail, $request->password, true);
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

        return $this->completeLogin($user_db, $request);
    }

    /**
     * Resolve a login identifier (email, machine code, or system id) to a
     * local user so any of them can be typed into the "email" field.
     */
    private function resolveLocalUser(?string $identifier): ?User
    {
        $identifier = trim((string) $identifier);
        if ($identifier === '') {
            return null;
        }

        return User::query()
            ->where('email', $identifier)
            ->orWhere('machine_code', $identifier)
            ->when(ctype_digit($identifier), fn ($q) => $q->orWhere('system_id', (int) $identifier))
            ->first();
    }

    /**
     * Log the resolved user in, stamp activity and redirect to the
     * intended page (or the learner dashboard).
     */
    private function completeLogin(User $user, Request $request)
    {
        Auth::login($user);
        $this->stampLastActive($user, force: true);
        $this->stampLastActiveByEmail($user->email ?? $request->email);
        Session::flash('success', 'تم تسجيل الدخول بنجاح');

        $intendedUrl = session()->pull('url.intended');
        if ($intendedUrl && str_contains($intendedUrl, 'exam')) {
            return redirect()->to($intendedUrl);
        }
        return redirect()->route('front.auth.dashboard');
    }

}
