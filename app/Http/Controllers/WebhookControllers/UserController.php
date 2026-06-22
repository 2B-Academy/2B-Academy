<?php

namespace App\Http\Controllers\WebhookControllers;

use App\Http\Controllers\Controller;
use App\Http\Traits\HelperTrait;
use App\Models\User;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class UserController extends Controller
{
    use HelperTrait;

    /**
     * @OA\Post(
     *     path="/webhooks/user/create-or-update",
     *     tags={"Webhooks"},
     *     summary="HR webhook: create or update an employee record.",
     *     description="Called by the HR system on employee create / update. Idempotent — matches existing records by `system_id`.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"system_id","name","email","machine_code","department_name"},
     *             @OA\Property(property="system_id",       type="integer", example=12345),
     *             @OA\Property(property="name",            type="string",  example="Ahmed Ali"),
     *             @OA\Property(property="email",           type="string",  format="email"),
     *             @OA\Property(property="phone",           type="string",  nullable=true),
     *             @OA\Property(property="machine_code",    type="string",  example="EMP-001"),
     *             @OA\Property(property="department_name", type="string",  example="Engineering")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Saved",   @OA\JsonContent(ref="#/components/schemas/SuccessResponse")),
     *     @OA\Response(response=422, ref="#/components/responses/ValidationError")
     * )
     */
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


    /**
     * @OA\Delete(
     *     path="/webhooks/user/delete/{system_id}",
     *     tags={"Webhooks"},
     *     summary="HR webhook: delete an employee record by HR system_id.",
     *     @OA\Parameter(
     *         name="system_id",
     *         in="path",
     *         required=true,
     *         description="HR system identifier of the employee",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Deleted", @OA\JsonContent(ref="#/components/schemas/SuccessResponse")),
     *     @OA\Response(response=400, description="Missing system_id", @OA\JsonContent(ref="#/components/schemas/ErrorResponse"))
     * )
     */
    public function destroy($system_id)
    {
        if(!$system_id)
            return $this->errorResponse('رقم المستخدم غير موجود');
        User::where('system_id', $system_id)->delete();
        return $this->successResponse('تم الحذف بنجاح');
    }

}
