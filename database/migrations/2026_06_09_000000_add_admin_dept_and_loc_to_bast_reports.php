<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bast_reports', function (Blueprint $table) {
            if (!Schema::hasColumn('bast_reports', 'admin_department')) {
                $table->string('admin_department')->nullable()->after('admin_name');
            }
            if (!Schema::hasColumn('bast_reports', 'admin_location')) {
                $table->string('admin_location')->nullable()->after('admin_department');
            }
        });

        // Populate existing records with admin_department and admin_location from user_reports if available
        if (Schema::hasTable('user_reports')) {
            $reports = DB::table('bast_reports')->get();
            foreach ($reports as $report) {
                $old = DB::table('user_reports')->find($report->id);
                if ($old) {
                    $giver = json_decode($old->giver_snapshot, true) ?: [];
                    DB::table('bast_reports')->where('id', $report->id)->update([
                        'admin_department' => $giver['department']['name'] ?? 'IT',
                        'admin_location' => $giver['location']['name'] ?? 'Wisma RMK (JAKARTA)',
                    ]);
                } else {
                    DB::table('bast_reports')->where('id', $report->id)->update([
                        'admin_department' => 'IT',
                        'admin_location' => 'Wisma RMK (JAKARTA)',
                    ]);
                }
            }
        } else {
            DB::table('bast_reports')->whereNull('admin_department')->update([
                'admin_department' => 'IT',
                'admin_location' => 'Wisma RMK (JAKARTA)',
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bast_reports', function (Blueprint $table) {
            if (Schema::hasColumn('bast_reports', 'admin_location')) {
                $table->dropColumn('admin_location');
            }
            if (Schema::hasColumn('bast_reports', 'admin_department')) {
                $table->dropColumn('admin_department');
            }
        });
    }
};
