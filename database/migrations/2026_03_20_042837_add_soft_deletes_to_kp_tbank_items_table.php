<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('kp_tbank_items', function (Blueprint $table) {
            $table->softDeletes(); // เพิ่มคอลัมน์ deleted_at (nullable timestamp)
        });
    }

    public function down()
    {
        Schema::table('kp_tbank_items', function (Blueprint $table) {
            $table->dropSoftDeletes(); // ลบคอลัมน์ออกหากมีการ Rollback
        });
    }
};
