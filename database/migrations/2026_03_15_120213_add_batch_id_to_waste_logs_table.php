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
    Schema::table('foodwaste_waste_logs', function (Blueprint $table) {
        // เพิ่ม batch_id เป็น Foreign Key และอนุญาตให้เป็น NULL ได้ (สำหรับขยะเก่าที่ยังไม่มีล็อต)
        $table->foreignId('batch_id')->nullable()->after('id')->constrained('foodwaste_compost_batches')->onDelete('set null');
    });
}

public function down()
{
    Schema::table('foodwaste_waste_logs', function (Blueprint $table) {
        $table->dropForeign(['batch_id']);
        $table->dropColumn('batch_id');
    });
}
};
