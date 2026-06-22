<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'learner_type')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('learner_type', 20)
                    ->nullable()
                    ->default('online')
                    ->after('department_name');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'learner_type')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('learner_type');
            });
        }
    }
};
