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
        Schema::table('users', function (Blueprint $table) {
            // เพิ่มคอลัมน์ โดยตั้งค่าเป็น nullable เพื่อไม่ให้กระทบ User เก่าที่ยังไม่กรอก
            $table->integer('age')->nullable()->after('email');
            $table->float('weight')->nullable()->after('age');
            $table->float('height')->nullable()->after('weight');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['age', 'weight', 'height']);
        });
    }
};
