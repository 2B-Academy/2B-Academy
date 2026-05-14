<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CourseSessionRequest extends FormRequest
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
        $rules = [
            'section_id' => 'required|exists:course_sections,id',
            'time_from' => 'required|date_format:H:i',
            'time_to' => 'required|date_format:H:i|after:time_from',
            'location' => 'required|max:255',
        ];
        if (request()->isMethod('put'))
        {
            $rules['title'] = 'required|max:255';
            $rules['session_date'] = 'required|date|after:today';
        }
        else
        {
            $rules['title.*'] = 'required|max:255';
            $rules['session_date.*'] = 'required|date|after:today';
        }
        return $rules;
    }

     public function attributes()
    {
           return [
               'section_id.required',
               'section_id.exists',
               'title.*.required',
               'title.*.max',
               'session_date.*.required',
               'session_date.*.date',
               'session_date.*.after',
               'time_from.required',
               'time_from.format',
               'time_to.required',
               'time_to.format',
               'location.required',
               'location.max',
           ];
    }

}
