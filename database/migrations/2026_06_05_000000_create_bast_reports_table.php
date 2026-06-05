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
        if (!Schema::hasTable('bast_reports')) {
            Schema::create('bast_reports', function (Blueprint $table) {
                $table->id();
                $table->string('bast_number')->unique();
                $table->unsignedInteger('user_id');
                $table->string('username');
                $table->string('user_email')->nullable();
                $table->string('user_nik')->nullable();
                $table->string('user_jobtitle')->nullable();
                $table->string('user_department')->nullable();
                $table->string('user_location')->nullable();
                $table->string('admin_name');
                $table->string('admin_title')->nullable();
                $table->date('date_printed');
                $table->string('perihal')->default('Penyerahan Aset (Inventaris Kantor)');
                $table->text('notes')->nullable();
                $table->json('assets_data');
                $table->timestamps();
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
        Schema::dropIfExists('bast_reports');
    }
};
