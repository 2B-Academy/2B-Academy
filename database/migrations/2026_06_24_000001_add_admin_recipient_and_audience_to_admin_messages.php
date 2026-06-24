<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * New inbox flow (Figma 717:70705):
 *
 *  • A message recipient can now also be a back-office admin
 *    (`admin_id`) — messages are targeted by DB role (any admin-guard
 *    role that has Learning-Operations access) on top of the existing
 *    learner (`user_id`) and instructor (`instructor_id`) recipients.
 *
 *  • `admin_messages.audience` snapshots the compose selection as
 *    groups (e.g. "All Learners", a role, or an explicit pick) so the
 *    Sent list/detail can render faithful "All <group>" labels without
 *    re-deriving intent from the flat recipient rows.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('admin_message_recipients', function (Blueprint $table) {
            if (! Schema::hasColumn('admin_message_recipients', 'admin_id')) {
                $table->unsignedBigInteger('admin_id')->nullable()->after('instructor_id');
                $table->foreign('admin_id')->references('id')->on('admins')->nullOnDelete();
                $table->index('admin_id');
            }
        });

        Schema::table('admin_messages', function (Blueprint $table) {
            if (! Schema::hasColumn('admin_messages', 'audience')) {
                $table->json('audience')->nullable()->after('body');
            }
        });
    }

    public function down(): void
    {
        Schema::table('admin_message_recipients', function (Blueprint $table) {
            if (Schema::hasColumn('admin_message_recipients', 'admin_id')) {
                $table->dropForeign(['admin_id']);
                $table->dropColumn('admin_id');
            }
        });

        Schema::table('admin_messages', function (Blueprint $table) {
            if (Schema::hasColumn('admin_messages', 'audience')) {
                $table->dropColumn('audience');
            }
        });
    }
};
