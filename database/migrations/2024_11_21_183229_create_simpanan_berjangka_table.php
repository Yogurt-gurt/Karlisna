<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSimpananBerjangkaTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('simpanan_berjangka', function (Blueprint $table) {
            $table->id();
            $table->string('no_simpanan')->unique(); // Nomor simpanan unik
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Relasi ke tabel users
            $table->string('bank'); // Nama bank
            $table->bigInteger('nominal'); // Nominal simpanan
            $table->integer('jangka_waktu_simpanan'); // Jangka waktu simpanan dalam bulan
            $table->bigInteger('jumlah_jasa_perbulan'); // Jumlah jasa simpanan per bulan
            $table->string('status_manager')->default('pending'); // Status manager: pending, approved, rejected
            $table->string('status_ketua')->default('pending'); // Status ketua: pending, approved, rejected
            $table->timestamps(); // created_at, updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('simpanan_berjangka');
    }
}
