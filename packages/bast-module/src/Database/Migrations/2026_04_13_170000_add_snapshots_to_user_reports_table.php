<?php

/*
|--------------------------------------------------------------------------
| Migration: add_snapshots_to_user_reports_table
|--------------------------------------------------------------------------
*/

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_reports', function (Blueprint $table) {
            if (!Schema::hasColumn('user_reports', 'recipient_snapshot')) {
                $table->json('recipient_snapshot')->nullable()->after('giver_id');
            }

            if (!Schema::hasColumn('user_reports', 'giver_snapshot')) {
                $table->json('giver_snapshot')->nullable()->after('recipient_snapshot');
            }

            if (!Schema::hasColumn('user_reports', 'header_snapshot')) {
                $table->json('header_snapshot')->nullable()->after('giver_snapshot');
            }
        });
    }

    public function down(): void
    {
        Schema::table('user_reports', function (Blueprint $table) {
            if (Schema::hasColumn('user_reports', 'header_snapshot')) {
                $table->dropColumn('header_snapshot');
            }

            if (Schema::hasColumn('user_reports', 'giver_snapshot')) {
                $table->dropColumn('giver_snapshot');
            }

            if (Schema::hasColumn('user_reports', 'recipient_snapshot')) {
                $table->dropColumn('recipient_snapshot');
            }
        });
    }
};
