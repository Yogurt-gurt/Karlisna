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
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->string('name'); // Nama user
            $table->string('email')->unique(); // Email unik
            $table->string('password'); // Password user
            $table->string('no_rekening')->nullable(); // Nomor rekening (opsional)
            $table->enum('roles', ['anggota', 'manager', 'ketua', 'admin', 'bendahara'])->default('anggota'); // Role user
            $table->unsignedBigInteger('anggota_id')->nullable(); // Kolom anggota_id untuk relasi
            $table->foreign('anggota_id') // Definisi foreign key
                ->references('id') // Mengacu ke kolom 'id'
                ->on('anggota') // Di tabel 'anggota'
                ->onDelete('set null') // Jika data dihapus, anggota_id di-set null
                ->onUpdate('cascade'); // Cascade jika data diperbarui
            $table->timestamps(); // Timestamps created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users'); // Hapus tabel users
    }
};
