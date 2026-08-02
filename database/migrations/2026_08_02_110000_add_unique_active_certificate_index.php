<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Enforces "one learner + one course = one ACTIVE certificate" in the
 * database, not just in PHP.
 *
 * CertificateService::issue() has always checked for an existing active
 * certificate inside a transaction, but `[user_id, course_id]` was only a
 * plain index — two concurrent requests (an exam submit racing the backfill,
 * say) could both pass the check and both insert. With the backfill about to
 * materialise thousands of historical rows, that guarantee needs teeth.
 *
 * MySQL has no partial indexes, so the invariant is expressed as a virtual
 * generated column that is NULL for anything not active. MySQL permits
 * duplicate NULLs in a unique index, so a learner may hold any number of
 * REVOKED certificates for a course while never holding two active ones —
 * and revoking then re-issuing still works.
 */
return new class extends Migration
{
    private const COLUMN = 'active_certificate_key';
    private const INDEX  = 'user_certificates_active_unique';

    public function up(): void
    {
        if (!Schema::hasTable('user_certificates') || !$this->isMysql()) {
            return;
        }
        if (Schema::hasColumn('user_certificates', self::COLUMN)) {
            return;
        }

        $this->revokeDuplicateActiveRows();

        DB::statement(sprintf(
            'ALTER TABLE `user_certificates` ADD COLUMN `%s` VARCHAR(64)'
            ." GENERATED ALWAYS AS (IF(`status` = 'active', CONCAT(`user_id`, '-', `course_id`), NULL)) VIRTUAL",
            self::COLUMN,
        ));

        DB::statement(sprintf(
            'CREATE UNIQUE INDEX `%s` ON `user_certificates` (`%s`)',
            self::INDEX,
            self::COLUMN,
        ));
    }

    public function down(): void
    {
        if (!Schema::hasTable('user_certificates') || !$this->isMysql()) {
            return;
        }
        if (!Schema::hasColumn('user_certificates', self::COLUMN)) {
            return;
        }

        DB::statement(sprintf('DROP INDEX `%s` ON `user_certificates`', self::INDEX));
        DB::statement(sprintf('ALTER TABLE `user_certificates` DROP COLUMN `%s`', self::COLUMN));
    }

    /**
     * The index cannot be created while duplicates exist. Keep the
     * oldest-issued active certificate per learner+course — it is the one the
     * learner has actually been shown — and revoke the rest rather than
     * deleting, so nothing is silently destroyed and the history stays
     * auditable.
     */
    private function revokeDuplicateActiveRows(): void
    {
        $duplicates = DB::table('user_certificates')
            ->select('user_id', 'course_id')
            ->where('status', 'active')
            ->groupBy('user_id', 'course_id')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($duplicates as $duplicate) {
            $keepId = DB::table('user_certificates')
                ->where('status', 'active')
                ->where('user_id', $duplicate->user_id)
                ->where('course_id', $duplicate->course_id)
                ->orderBy('issued_at')
                ->orderBy('id')
                ->value('id');

            DB::table('user_certificates')
                ->where('status', 'active')
                ->where('user_id', $duplicate->user_id)
                ->where('course_id', $duplicate->course_id)
                ->where('id', '!=', $keepId)
                ->update([
                    'status'     => 'revoked',
                    'revoked_at' => now(),
                    'updated_at' => now(),
                ]);
        }
    }

    private function isMysql(): bool
    {
        return DB::connection()->getDriverName() === 'mysql';
    }
};
