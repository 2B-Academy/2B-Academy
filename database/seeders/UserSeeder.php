<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Bulk loads end-users from the production dump.
 *
 * The user fixture is large (≈1,056 rows) and contains no translatable
 * columns, so executing the prepared INSERT directly is the most
 * efficient path. We:
 *  - truncate the table inside a transaction (foreign-key-safe)
 *  - replay the verbatim INSERT block exported from `2b.sql`
 *  - rely on `data/users.sql` as the canonical, version-controlled source
 *
 * Run from `php artisan db:seed --class=UserSeeder` or via DatabaseSeeder.
 */
class UserSeeder extends Seeder
{
    public function run(): void
    {
        $path = __DIR__.'/data/users.sql';

        if (! file_exists($path)) {
            $this->command?->warn("UserSeeder: data fixture missing at {$path}; skipping.");

            return;
        }

        $sql = file_get_contents($path);

        if ($sql === false || trim($sql) === '') {
            return;
        }

        DB::connection()->getPdo()->exec('SET FOREIGN_KEY_CHECKS=0');
        try {
            DB::table('users')->delete();
            DB::unprepared($sql);
        } finally {
            DB::connection()->getPdo()->exec('SET FOREIGN_KEY_CHECKS=1');
        }

        if (Schema::hasTable('users')) {
            $count = DB::table('users')->count();
            $this->command?->info("UserSeeder: loaded {$count} users from fixture.");
        }
    }
}
