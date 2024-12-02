<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserIdToRekeningsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rekenings', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->after('id'); // Tambahkan kolom user_id
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade'); // Tambahkan relasi
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rekenings', function (Blueprint $table) {
            $table->dropForeign(['user_id']); // Hapus foreign key
            $table->dropColumn('user_id');    // Hapus kolom user_id
        });
    }
}
