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
        Schema::create('pinjaman_emergencies', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_pinjaman')->unique();
            $table->integer('nominal_pinjaman');
            $table->integer('jangka_waktu');
            $table->integer('nominal_angsuran');
            $table->string('status')->default('Pending');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); // Foreign key ke tabel users yang bisa null
            $table->foreignId('rekening_id')->nullable()->constrained('rekenings')->onDelete('set null'); // Foreign key ke tabel rekenings yang bisa null
            // Menambahkan status persetujuan admin 1, admin 2, dan ketua
            $table->string('status_manager')->nullable(); // Status persetujuan Admin 1
            $table->string('status_ketua')->nullable();
            $table->string('status_bendahara')->nullable(); // Status persetujuan Admin 2
            // Menambahkan kolom checkbox
            $table->boolean('checkbox_syarat_3')->default(false); 
            $table->boolean('checkbox_syarat_4')->default(false); 
            $table->boolean('checkbox_syarat_5')->default(false); 
            // Menambahkan kolom keterangan
            $table->text('keterangan')->nullable(); // Kolom untuk keterangan tambahan
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pinjaman_emergencies');
    }
};
