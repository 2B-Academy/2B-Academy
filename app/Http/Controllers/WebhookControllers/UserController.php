<?php

namespace App\Http\Controllers\WebhookControllers;

use App\Http\Controllers\Controller;
use App\Http\Traits\HelperTrait;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    use HelperTrait;

    public function createOrUpdate(Request $request)
    {
       $request->validate([
           'system_id' => 'required',
           'name' => 'required',
           'email' => 'required',
           'phone' => 'nullable',
           'machine_code' => 'required',
           'department_name' => 'required',
       ]);
       User::updateOrCreate(['system_id' => $request->system_id],[
           'name' => $request->name,
           'email' => $request->email,
           'phone' => $request->phone,
           'machine_code' => $request->machine_code,
           'department_name' => $request->department_name,
       ]);
       return $this->successResponse('تم الحفظ بنجاح');
    }


    public function destroy($system_id)
    {
        if(!$system_id)
            return $this->errorResponse('رقم المستخدم غير موجود');
        User::where('system_id', $system_id)->delete();
        return $this->successResponse('تم الحذف بنجاح');
    }

}
