<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRekeningsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rekenings', function (Blueprint $table) {
            $table->id(); // Primary Key
            $table->string('nomor_rekening')->unique(); // Nomor rekening (unik)
            $table->string('jenis_bank'); // Jenis bank
            $table->boolean('is_rekening_utama')->default(false); // Apakah rekening utama
            $table->unsignedBigInteger('user_id'); // Foreign key ke tabel users
            $table->string('nama'); // Nama pemilik rekening
            $table->timestamps(); // Kolom created_at dan updated_at

            // Foreign Key Constraint
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rekenings');
    }
}
