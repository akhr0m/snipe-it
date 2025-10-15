<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_reports', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->unique(); // ID dari tabel users, harus unik
            $table->string('report_number')->unique(); // Nomor BAST yang unik
            $table->timestamps(); // Kolom created_at dan updated_at

            // Menambahkan foreign key untuk relasi ke tabel users
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_reports');
    }
};
