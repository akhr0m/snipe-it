<?php

/*
|--------------------------------------------------------------------------
| Migration: create_user_reports_table
|--------------------------------------------------------------------------
*/

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_reports', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->nullable();
            $table->integer('recipient_id')->unsigned()->nullable();
            $table->integer('giver_id')->unsigned()->nullable();
            $table->string('report_number')->unique()->nullable();
            $table->json('assets_snapshot')->nullable();
            $table->timestamp('handover_date')->nullable();
            $table->json('recipient_snapshot')->nullable();
            $table->json('giver_snapshot')->nullable();
            $table->json('header_snapshot')->nullable();
            $table->timestamps();

            // optional foreign keys
            // $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_reports');
    }
};
