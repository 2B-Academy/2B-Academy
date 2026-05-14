<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class AboutRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'about'        => 'nullable|array',
            'about.ar'     => 'nullable|string',
            'about.en'     => 'nullable|string',
            'mission'      => 'nullable|array',
            'mission.ar'   => 'nullable|string',
            'mission.en'   => 'nullable|string',
            'vision'       => 'nullable|array',
            'vision.ar'    => 'nullable|string',
            'vision.en'    => 'nullable|string',
            'goals'        => 'nullable|array',
            'goals.ar'     => 'nullable|string',
            'goals.en'     => 'nullable|string',
            'image'        => 'nullable|image|mimes:png,jpg,jpeg,webp|max:2000',
        ];
    }
}
