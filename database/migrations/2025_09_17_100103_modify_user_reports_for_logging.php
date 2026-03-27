<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
{
    Schema::table('user_reports', function (Blueprint $table) {
        // 1. Tangani user_id tanpa aturan Unique
        if (Schema::hasColumn('user_reports', 'user_id')) {
            try {
                $table->dropForeign(['user_id']); 
                $table->dropUnique('user_reports_user_id_unique');
            } catch (\Exception $e) {}
        } else {
            $table->integer('user_id')->unsigned()->nullable();
        }

        // 2. Kolom Relasi ID
        if (!Schema::hasColumn('user_reports', 'recipient_id')) {
            $table->integer('recipient_id')->unsigned();
        }
        if (!Schema::hasColumn('user_reports', 'giver_id')) {
            $table->integer('giver_id')->unsigned();
        }

        // 3. Kolom Data Laporan (KOLOM YANG HILANG SEBELUMNYA)
        if (!Schema::hasColumn('user_reports', 'report_number')) {
            $table->string('report_number')->nullable();
        }
        if (!Schema::hasColumn('user_reports', 'assets_snapshot')) {
            $table->json('assets_snapshot')->nullable();
        }
        if (!Schema::hasColumn('user_reports', 'handover_date')) {
            $table->timestamp('handover_date')->nullable();
        }

        // 4. Timestamps
        if (!Schema::hasColumn('user_reports', 'created_at')) {
            $table->timestamp('created_at')->nullable();
        }
        if (!Schema::hasColumn('user_reports', 'updated_at')) {
            $table->timestamp('updated_at')->nullable();
        }
    });
}
};
