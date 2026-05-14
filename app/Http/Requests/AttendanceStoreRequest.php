<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttendanceStoreRequest extends FormRequest
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
            'course_id' => 'required|exists:courses,id',
            'user_id'   => 'required|exists:users,id',
            'status'   => 'nullable',
        ];
    }

     public function attributes()
    {
           return [
               'course_id.required',
               'course_id.exists',
               'user_id.required',
               'user_id.exists',
           ];
    }

}
