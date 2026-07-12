<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class LectureProgressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // `progress` stays required for legacy callers that never send
            // `confirmed` (numeric watch % is still how they signal
            // completion). Once `confirmed` is present — the "Did you
            // complete this video?/read this article?" Yes/No prompt, or
            // the "Mark as complete" click for link modules — it alone
            // decides completion and `progress` becomes optional.
            'progress'  => 'required_without:confirmed|integer|min:0|max:100',
            'confirmed' => 'nullable|boolean',
        ];
    }
}
