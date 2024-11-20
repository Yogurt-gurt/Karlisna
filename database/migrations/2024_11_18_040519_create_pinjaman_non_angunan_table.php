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
        Schema::create('pinjaman_non_angunan', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_pinjaman')->unique();
            $table->integer('nominal_pinjaman');
            $table->integer('jangka_waktu');
            $table->integer('nominal_angsuran');
            $table->string('status')->default('Pending');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); // Foreign key ke tabel users yang bisa null
            $table->foreignId('rekening_id')->nullable()->constrained('rekenings')->onDelete('set null'); // Foreign key ke tabel rekenings yang bisa null
            // Menambahkan status persetujuan admin 1 dan admin 2
            $table->string('status_manager')->nullable(); // Status persetujuan Admin 1
            $table->string('status_ketua')->nullable();
            $table->string('status_bendahara')->nullable(); // Status persetujuan Admin 2
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pinjaman_non_angunan');
    }
};
