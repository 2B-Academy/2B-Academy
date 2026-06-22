<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Mobile;

use App\Services\Mobile\MobileSettings;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Validate cohort feedback (Figma 543:41637 → 543:41844).
 *
 * Every constraint is settings-driven:
 *   - min/max value           ← rating_min_value / rating_max_value
 *   - comment required cutoff ← rating_comment_required_at_or_below
 *   - comment max length      ← rating_comment_max_length
 */
final class SubmitRatingRequest extends FormRequest
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

    /**
     * Safety net for clients that POST a JSON body without a proper
     * `Content-Type: application/json` header (a very common Swagger /
     * Postman misconfiguration). In that case Laravel never populates
     * the input bag from the body, so `rating` looks "missing" even
     * though it was sent. We decode the raw body and merge it in.
     */
    protected function prepareForValidation(): void
    {
        if ($this->input('rating') !== null) {
            return;
        }

        $raw = trim((string) $this->getContent());
        if ($raw === '') {
            return;
        }

        $decoded = json_decode($raw, true);
        if (is_array($decoded)) {
            $this->merge($decoded);
        }
    }

    public function rules(): array
    {
        $min = $this->settings->ratingMinValue();
        $max = $this->settings->ratingMaxValue();
        $commentMax = $this->settings->ratingCommentMaxLength();

        return [
            'rating'  => ['required', 'integer', "min:{$min}", "max:{$max}"],
            'comment' => ['nullable', 'string', "max:{$commentMax}"],
        ];
    }

    /**
     * Conditional rule — for ratings at or below the configured cutoff,
     * the comment must be present and non-empty.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v): void {
            $rating  = (int) $this->input('rating', 0);
            $cutoff  = $this->settings->ratingCommentRequiredAtOrBelow();
            $comment = trim((string) $this->input('comment', ''));

            if ($rating > 0 && $rating <= $cutoff && $comment === '') {
                $v->errors()->add(
                    'comment',
                    __('validation.required', ['attribute' => 'comment']),
                );
            }
        });
    }
}
