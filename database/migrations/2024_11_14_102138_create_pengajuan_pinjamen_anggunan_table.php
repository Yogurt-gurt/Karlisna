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
        Schema::create('pengajuan_pinjamen_anggunan', function (Blueprint $table) {
            $table->id();
            $table->integer('nominal_pinjaman');
            $table->integer('jangka_waktu');
            $table->integer('nominal_angsuran');
            $table->string('status')->default('Pending');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('rekening_id')->nullable()->constrained('rekenings')->onDelete('set null');
            
            // Kolom tambahan untuk pengajuan dengan anggunan
            $table->string('jenis_anggunan'); // Kolom jenis anggunan
            $table->string('file_anggunan'); // Kolom untuk file unggahan anggunan

            // Status persetujuan admin
            $table->string('status_admin1')->nullable();
            $table->string('status_admin2')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuan_pinjamen_anggunan');
    }
};
