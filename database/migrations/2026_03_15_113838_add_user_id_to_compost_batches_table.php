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
    Schema::table('foodwaste_compost_batches', function (Blueprint $table) {
        // เพิ่มคอลัมน์ user_id ต่อท้าย id
        $table->foreignId('user_id')->after('id')->constrained()->onDelete('cascade');
    });
}

public function down()
{
    Schema::table('foodwaste_compost_batches', function (Blueprint $table) {
        $table->dropForeign(['user_id']);
        $table->dropColumn('user_id');
    });
}
};
