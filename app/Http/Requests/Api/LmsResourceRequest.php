<?php

namespace App\Http\Requests\Api;

use App\Http\Traits\AcceptsEnumIds;
use Illuminate\Foundation\Http\FormRequest;

class LmsResourceRequest extends FormRequest
{
    use AcceptsEnumIds;

    public function authorize(): bool
    {
        return true;
    }

    /**
     * Tell {@see AcceptsEnumIds} which incoming fields are enum IDs and
     * which backend enum they map to. The frontend's `p-dropdown`s now
     * post a numeric `id`; this trait normalizes it back to the string
     * code (`"article"`, `"link"`, `"file"`) so the rest of the
     * validator and storage layer remains untouched.
     *
     * @return array<string, string>
     */
    protected function enumFieldMap(): array
    {
        return [
            'type' => 'resource_type',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->normalizeEnumIdsToCodes();
    }

    public function rules(): array
    {
        // On update (PUT) the file field is never mandatory — the existing file is kept.
        $isUpdate   = $this->isMethod('PUT') || $this->isMethod('PATCH');
        $fileRules  = $isUpdate
            ? ['nullable', 'file', 'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx', 'max:51200']
            : ['nullable', 'file', 'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx', 'max:51200', 'required_if:type,file'];

        return [
            'title'                  => ['required', 'string', 'max:255'],
            'type'                   => ['required', 'in:article,link,file'],
            'content'                => ['nullable', 'string', 'required_if:type,article'],
            'url'                    => ['nullable', 'url', 'required_if:type,link'],
            'file'                   => $fileRules,
            'qualification_skill_id' => ['nullable', 'integer', 'exists:qualification_skills,id'],
        ];
    }
}
