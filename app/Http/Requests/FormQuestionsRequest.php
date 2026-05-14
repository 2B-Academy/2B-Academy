<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FormQuestionsRequest extends FormRequest
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
            'question' => 'required|string',
            'type'     => 'required|in:radio,yes_no,text',
        ];

        if ($this->type === 'radio') {
            $rules['radio_answer'] = 'required|array|size:4';
            $rules['radio_answer.*'] = 'required|string|max:255';
            $rules['radio_answer_check.is_true'] = 'required';
        }

        if ($this->type === 'yes_no') {
            $rules['yes_no_answer'] = 'required|array|size:2';
            $rules['yes_no_answer.*'] = 'required|string|max:255';
            $rules['yes_no_answer_check.is_true'] = 'required|boolean';
        }

        return $rules;
    }

     public function attributes()
    {
           return [
               'question.required',
               'type.required',
               'type.in',
           ];
    }

}
