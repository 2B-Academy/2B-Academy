<?php

namespace Database\Seeders\Concerns;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Helper for seeders that need to remain forward/backward compatible
 * with schemas where individual columns may have been added or
 * removed by later migrations.
 *
 * Usage:
 *   $this->schemaAwareUpsert('course_exams', $rows, ['id'], ['title', 'degree']);
 *
 * The trait inspects the live table columns once per call and quietly
 * drops any keys from `$rows`/`$updateColumns` that don't exist on the
 * target table, preventing 1054 "Unknown column" errors.
 */
trait SchemaAwareUpsert
{
    /**
     * @param  array<int, array<string, mixed>>  $rows
     * @param  array<int, string>  $uniqueBy
     * @param  array<int, string>|null  $update Columns to refresh on duplicate key.
     *   - `null` (default): refresh every non-`id` column.
     *   - `[]` (empty array): table has no updatable columns (pure pivot);
     *     falls back to `insertOrIgnore` to remain idempotent.
     *   - non-empty list: refresh exactly those columns.
     */
    protected function schemaAwareUpsert(string $table, array $rows, array $uniqueBy, ?array $update = null): int
    {
        if ($rows === [] || ! Schema::hasTable($table)) {
            return 0;
        }

        $columns = array_flip(Schema::getColumnListing($table));

        $payload = array_map(
            static fn (array $row) => array_intersect_key($row, $columns),
            $rows
        );

        $payload = array_values(array_filter($payload, static fn (array $row) => $row !== []));

        if ($payload === []) {
            return 0;
        }

        $emptyUpdate = $update === [];
        if ($update === null) {
            $update = array_values(array_diff(array_keys($payload[0]), ['id']));
        }
        $update = array_values(array_intersect($update, array_keys($columns)));

        foreach (array_chunk($payload, 500) as $chunk) {
            if ($emptyUpdate || $update === []) {
                DB::table($table)->insertOrIgnore($chunk);
            } else {
                DB::table($table)->upsert($chunk, $uniqueBy, $update);
            }
        }

        return count($payload);
    }
}
