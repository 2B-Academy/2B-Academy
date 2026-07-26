<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Http;

class ReCaptcha implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {

    }
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // config() (not env()) so the secret survives `config:cache`;
        // otherwise it resolves to null in production and every captcha
        // verification fails, making the guarded form unsubmittable.
        $response = Http::get("https://www.google.com/recaptcha/api/siteverify", [
            'secret'   => config('services.recaptcha.secret'),
            'response' => $value,
        ]);

        return (bool) ($response->json()['success'] ?? false);
    }
    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('text.invalid_captcha');
    }
}
