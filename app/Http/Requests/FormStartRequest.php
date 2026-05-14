<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FormStartRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|max:255',
            'machine_code' => 'required|max:255|exists:users,machine_code',
            'start_at' => 'nullable',
        ];
    }

     public function messages()
    {
        return [
            'name.required' => 'يجب ادخال الأسم من فضلك',
            'name.max' => 'يجب ان يكون الأسم لا يزيد عن 255 حرف',
            'machine_code.required' => 'يجب ادخال كود الموظف من فضلك',
            'machine_code.max' => 'يجب ان يكون كود الموظف لا يزيد عن 255 حرف',
            'machine_code.exists' => 'كود الموظف غير موجود بالنظام',
        ];
    }

}
