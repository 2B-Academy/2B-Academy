<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Mobile;

use App\Services\Mobile\MobileSettings;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Admin-side payload for issuing/rotating a session passcode.
 *
 *   - `expires_at` (optional ISO 8601 timestamp) overrides the default
 *      validity window from `attendance_window_minutes`.
 *   - `length` (optional) overrides the platform passcode length for
 *      this single session (e.g. for a high-security cohort).
 */
final class IssuePasscodeRequest extends FormRequest
{
    public function __construct(
        private readonly MobileSettings $settings,
        array $query = [],
        array $request = [],
        array $attributes = [],
        array $cookies = [],
        array $files = [],
        array $server = [],
        $content = null,
    ) {
        parent::__construct($query, $request, $attributes, $cookies, $files, $server, $content);
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $defaultLength = $this->settings->attendancePasscodeLength();

        return [
            'expires_at' => ['nullable', 'date', 'after:now'],
            'length'     => ['nullable', 'integer', 'min:3', 'max:10'],
        ];
    }
}
