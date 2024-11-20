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
        Schema::create('simpanan_berjangka', function (Blueprint $table) {
            $table->id(); // Primary key autoincrement 'id'
            $table->string('invoice_number')->unique(); // Unique invoice number
            $table->decimal('amount', 15, 2); // Amount in decimal format
            $table->integer('jangka_waktu'); // Duration in months
            $table->decimal('jumlah_jasa', 15, 2); // Service fee or interest
            $table->string('bank'); // Bank name or path
            $table->string('virtual_account_number')->nullable(); // Nullable virtual account number
            $table->string('status')->default('pending'); // Default status to 'pending'
            $table->timestamps(); // created_at and updated_at timestamps
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('simpanan_berjangka');
    }
};
