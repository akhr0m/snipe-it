<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('bast_reports') && !Schema::hasColumn('bast_reports', 'admin_nik')) {
            Schema::table('bast_reports', function (Blueprint $table) {
                $table->string('admin_nik')->nullable()->after('admin_name');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('bast_reports') && Schema::hasColumn('bast_reports', 'admin_nik')) {
            Schema::table('bast_reports', function (Blueprint $table) {
                $table->dropColumn('admin_nik');
            });
        }
    }
};
