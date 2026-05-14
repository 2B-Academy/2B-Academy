<?php

namespace App\Http\Requests;

use App\Rules\ReCaptcha;
use Illuminate\Foundation\Http\FormRequest;

class CareerApplicationRequest extends FormRequest
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
        $required_file = 'required|mimes:pdf|max:2000';
        return [
               'career_id' => 'nullable',
               'name'   => 'required|max:255',
               'email'  => 'required|email|max:255',
               'phone'  => 'required|max:255',
               'cv'     => $required_file,
               'current_salary'  => 'required|max:255',
               'expected_salary' => 'required|max:255',
               'number_years_experience' => 'required|max:255',
               'nationality' => 'required|max:255',
               'notice_period' => 'required|max:255',
               'degree_id' => 'required|max:255',
               'experience_in_real_estate' => 'required|max:255|in:yes,no',
               'marital_status' => 'required|max:255|in:single,married',
               'english_level'=> 'required|max:255|in:low,good,excellent',
                'g-recaptcha-response' => ['required', new ReCaptcha],

        ];
    }

}
