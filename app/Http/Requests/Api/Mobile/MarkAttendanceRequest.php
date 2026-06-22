<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Mobile;

use App\Services\Mobile\MobileSettings;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Validate the passcode submission for S-06 (Mark Present).
 *
 * The passcode length is not hardcoded — it's pulled from the
 * `mobile_attendance.attendance_passcode_length` setting so the
 * platform can move between 4/5/6-digit codes without a redeploy.
 */
final class MarkAttendanceRequest extends FormRequest
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
        $length = $this->settings->attendancePasscodeLength();

        return [
            'course_id'  => ['required', 'integer', 'min:1', 'exists:courses,id'],
            'session_id' => ['nullable', 'integer', 'min:1', 'exists:course_sessions,id'],
            'passcode'   => [
                'required',
                'string',
                "size:{$length}",
                'regex:/^[0-9]+$/',
            ],
        ];
    }

    public function messages(): array
    {
        $length = $this->settings->attendancePasscodeLength();

        return [
            'passcode.size'  => __('messages.mobile.attendance_invalid_code'),
            'passcode.regex' => __('messages.mobile.attendance_invalid_code'),
            'passcode.required' => __('messages.mobile.attendance_invalid_code'),
        ];
    }
}
