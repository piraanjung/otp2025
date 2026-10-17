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
        Schema::table('kp_tbank_items', function (Blueprint $table) {
            // 1. เพิ่มคอลัมน์ใหม่
            $table->string('unit_bank')->default('กก.')->after('kp_itemsname');
            $table->string('unit_kiosk')->nullable()->after('unit_bank');

            // 2. ลบคอลัมน์ที่ไม่เอาออก
            $table->dropColumn('item_for_machine');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('kp_tbank_items', function (Blueprint $table) {
            // ย้อนกลับ: ลบที่เพิ่ม และเพิ่มที่ลบกลับมา
            $table->dropColumn(['unit_bank', 'unit_kiosk']);
            $table->tinyInteger('item_for_machine')->default(0);
        });
    }
};
