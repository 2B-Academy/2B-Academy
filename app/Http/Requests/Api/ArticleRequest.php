<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class ArticleRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $imageRule = $this->isMethod('PUT')
            ? 'nullable|image|mimes:png,jpg,jpeg,webp,svg,gif|max:2000'
            : 'required|image|mimes:png,jpg,jpeg,webp,svg,gif|max:2000';

        return [
            'type'              => 'required|in:news,blogs,event',
            'title'             => 'required|array',
            'title.ar'          => 'required|string|max:255',
            'title.en'          => 'nullable|string|max:255',
            'description'       => 'required|array',
            'description.ar'    => 'required|string',
            'description.en'    => 'nullable|string',
            'slug'              => 'required|string|max:255|unique:articles,slug,' . ($this->article?->id ?? 'NULL'),
            'date_publish'      => 'nullable|date',
            'image'             => $imageRule,
            'is_home'           => 'nullable|boolean',
            'active'            => 'nullable|boolean',
        ];
    }
}
