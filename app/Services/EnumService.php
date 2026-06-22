<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\EnumRegistry;
use Illuminate\Support\Facades\Lang;

/**
 * Resolves the localized `{id, value, code, description?}` option set for
 * every enum registered in {@see EnumRegistry}.
 *
 *   - `id`    1-indexed numeric identifier (stable for the lifetime of the
 *             enum's value order). This is what the frontend sends on
 *             POST/PUT.
 *   - `value` localized display label rendered in the dropdown.
 *   - `code`  underlying string machine code (e.g. "online"). The DB columns
 *             still store this; FormRequests use {@see AcceptsEnumIds} to
 *             translate `id` → `code` on the way in.
 *
 * Labels are pulled from `resources/lang/{en,ar}/enums.php`. If a translation
 * key is missing the code itself is used as a graceful fallback so the
 * frontend never receives an empty display string.
 */
class EnumService
{
    /**
     * Build the full options payload for a single enum in the requested locale.
     *
     * @return array<int, array{id: int, value: string, code: string, description: ?string}>
     */
    public function options(string $name, ?string $locale = null): array
    {
        if (! EnumRegistry::exists($name)) {
            return [];
        }

        $locale       = $locale ?: app()->getLocale();
        $codes        = EnumRegistry::values($name);
        $hasDesc      = EnumRegistry::hasDescriptions($name);
        $labelPrefix  = "enums.$name";

        $options = [];
        foreach (array_values($codes) as $pos => $code) {
            $labelKey = "$labelPrefix.$code";
            $label    = Lang::has($labelKey, $locale)
                ? __($labelKey, [], $locale)
                : $code;

            $description = null;
            if ($hasDesc) {
                $descKey = "$labelPrefix.{$code}_desc";
                if (Lang::has($descKey, $locale)) {
                    $description = __($descKey, [], $locale);
                }
            }

            $options[] = [
                'id'          => $pos + 1, // 1-indexed numeric id
                'value'       => is_string($label) ? $label : $code,
                'code'        => $code,
                'description' => $description,
            ];
        }

        return $options;
    }

    /**
     * Return every enum keyed by its name. Used by the frontend at boot to
     * prime its cache in a single round-trip.
     *
     * @return array<string, array<int, array{id: int, value: string, code: string, description: ?string}>>
     */
    public function all(?string $locale = null): array
    {
        $locale = $locale ?: app()->getLocale();
        $out    = [];
        foreach (EnumRegistry::names() as $name) {
            $out[$name] = $this->options($name, $locale);
        }
        return $out;
    }

    /**
     * Translate a numeric dropdown id into the underlying string code.
     * Returns null when the id is out of range.
     */
    public function codeForId(string $name, int $id): ?string
    {
        $codes = EnumRegistry::values($name);
        return $codes[$id - 1] ?? null; // ids are 1-indexed
    }

    /**
     * Reverse mapping — find the numeric dropdown id for a given string code.
     * Used by API resources that want to surface `course_type_id` alongside
     * the existing string column.
     */
    public function idForCode(string $name, ?string $code): ?int
    {
        if ($code === null) {
            return null;
        }
        $codes = EnumRegistry::values($name);
        $pos   = array_search($code, $codes, true);
        return $pos === false ? null : $pos + 1;
    }

    /**
     * @return array<int, string>
     */
    public function names(): array
    {
        return EnumRegistry::names();
    }
}
