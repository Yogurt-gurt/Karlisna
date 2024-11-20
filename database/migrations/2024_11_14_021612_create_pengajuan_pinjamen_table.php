<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('pengajuan_pinjamen', function (Blueprint $table) {
            $table->id();
            $table->integer('nominal_pinjaman');
            $table->integer('jangka_waktu');
            $table->integer('nominal_angsuran');
            $table->string('status')->default('Pending');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); // Foreign key ke tabel users yang bisa null
            $table->foreignId('rekening_id')->nullable()->constrained('rekenings')->onDelete('set null'); // Foreign key ke tabel rekenings yang bisa null
            // Menambahkan status persetujuan admin 1 dan admin 2
            $table->string('status_admin1')->nullable(); // Status persetujuan Admin 1
            $table->string('status_admin2')->nullable(); // Status persetujuan Admin 2
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuan_pinjamen');
    }
};
