<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Adds the `min_passing_attendance` Platform Config row.
 *
 * "Certificate Awarded Based On" has always offered an "Attendance only"
 * option, but no setting existed to say *how much* attendance — the
 * threshold was hidden in a per-course column no API could set. With
 * App\Services\CertificatePolicy reading the rule from Platform Config,
 * this row is the attendance half of it.
 *
 * Runs as a migration rather than seeder-only so existing installs (and the
 * production import, whose `settings` table predates every platform key)
 * pick it up on deploy. Insert-if-absent, so an admin's edited value is
 * never overwritten by a re-run.
 */
return new class extends Migration
{
    private const KEY = 'min_passing_attendance';

    public function up(): void
    {
        if (DB::table('settings')->where('key', self::KEY)->exists()) {
            return;
        }

        DB::table('settings')->insert([
            'key'        => self::KEY,
            'value'      => '70',
            'type'       => 'number',
            'label'      => 'Min Passing Attendance (%)',
            'module'     => 'platform',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        DB::table('settings')->where('key', self::KEY)->delete();
    }
};
