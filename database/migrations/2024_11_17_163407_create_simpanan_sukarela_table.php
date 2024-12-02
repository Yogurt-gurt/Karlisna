<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class CreateSimpananSukarelaTable extends Migration


{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('simpanan_sukarela', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->bigInteger('amount');
            $table->string('virtual_account_number')->nullable();
            $table->string('status')->default('pending'); // pending, success, failed, expired
            $table->timestamps(); // created_at, updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('simpanan_sukarela');
    }
};
