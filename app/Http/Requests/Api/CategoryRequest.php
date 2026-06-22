<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            /**
             * `Category::$translatable = ['name']` (Spatie) — admins submit
             * the localised pair `name[en]` / `name[ar]` from the bilingual
             * dialog. Mirrors `PublicNotificationRequest::rules()`.
             */
            'name'    => 'required|array',
            'name.ar' => 'required|string|max:255',
            'name.en' => 'nullable|string|max:255',
            'active'  => 'nullable|boolean',
            /**
             * Categories no longer carry an image (removed per design review).
             * The rule stays `nullable` purely so any stray legacy multipart
             * submission still validates instead of 422-ing.
             */
            'logo'    => 'nullable|image|mimes:png,jpg,jpeg,webp,svg|max:2048',
        ];
    }
}
