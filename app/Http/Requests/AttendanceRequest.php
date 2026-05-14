<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class AttendanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_machine_code' => ['required'],
            'course_id' => ['required'],
        ];
    }

    public function messages(): array
    {
        return [
            'user_machine_code.required' => 'الكود الوظيفي مطلوب',
            'user_machine_code.exists' => 'الكود الوظيفي غير صحيح',
            'course_id.required' => 'الدورة التدريبية مطلوبة',
            'course_id.exists' => 'الدورة التدريبية غير صحيحة',
        ];
    }
}
