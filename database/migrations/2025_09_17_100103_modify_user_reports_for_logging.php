<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_reports', function (Blueprint $table) {
//            $table->increments('id');
            $table->integer('recipient_id')->unsigned();
            $table->integer('giver_id')->unsigned();
            $table->string('report_number')->unique();
            $table->json('assets_snapshot')->nullable();
            $table->timestamp('handover_date')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('recipient_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('giver_id')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_reports');
    }
};
