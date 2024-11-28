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
        Schema::create('pinjamen', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->foreignId('user_id')->constrained()->restrictOnUpdate()->restrictOnDelete();
            $table->string('nomor_pinjaman')->unique();
            $table->integer('nominal');
            $table->date('tanggal_pinjaman');
            $table->integer('nominal_angsuran');
            // $table->date('tujuan_pinjaman');
            $table->string('jenis_pinjaman');
            $table->enum('jenis_angungan', ['SERTIFIKAT TANAH','SERTIFIKAT RUMAH','BPKB KENDARAAN','SURAT BERHARGA LAINNYA'])->nullable();
            $table->enum('tenor' ,['3 Bulan','6 Bulan','9 Bulan','12 Bulan','15 bulan','18 bulan','24 bulan','27 bulan']);
            $table->string('image')->nullable();
            $table->enum('status_manager', ['Pengajuan', 'Diterima', 'Ditolak'])->default('Pengajuan');
            $table->enum('status_ketua', ['Pengajuan', 'Diterima', 'Ditolak'])->default('Pengajuan');
            $table->enum('status', ['Pengajuan', 'Diterima', 'Ditolak'])->default('Pengajuan');
            $table->string('keterangan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pinjamen');
    }
};
