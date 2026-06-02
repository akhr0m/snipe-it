<?php

/*
|--------------------------------------------------------------------------
| Migration: modify_user_reports_for_logging
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
            try {
                if (Schema::hasColumn('user_reports', 'user_id')) {
                    $table->dropForeign(['user_id']);
                }
            } catch (\Exception $e) {
                // ignore
            }

            if (!Schema::hasColumn('user_reports', 'recipient_id')) {
                $table->integer('recipient_id')->unsigned()->nullable();
            }
            if (!Schema::hasColumn('user_reports', 'giver_id')) {
                $table->integer('giver_id')->unsigned()->nullable();
            }

            if (!Schema::hasColumn('user_reports', 'report_number')) {
                $table->string('report_number')->nullable();
            }
            if (!Schema::hasColumn('user_reports', 'assets_snapshot')) {
                $table->json('assets_snapshot')->nullable();
            }
            if (!Schema::hasColumn('user_reports', 'handover_date')) {
                $table->timestamp('handover_date')->nullable();
            }

            if (!Schema::hasColumn('user_reports', 'created_at')) {
                $table->timestamp('created_at')->nullable();
            }
            if (!Schema::hasColumn('user_reports', 'updated_at')) {
                $table->timestamp('updated_at')->nullable();
            }
        });
    }
};
