<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migration.
     */
    public function up()
    {
        Schema::table('rekenings', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->after('id'); // Kolom foreign key
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Rollback migration.
     */
    public function down()
    {
        Schema::table('rekenings', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};
