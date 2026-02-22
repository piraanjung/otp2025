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
        Schema::table('kp_purchase_transactions', function (Blueprint $table) {
            // เพิ่มคอลัมน์ total_carbon_saved ต่อท้าย total_points
            // ใช้ decimal(12, 4) เพื่อเก็บทศนิยม 4 ตำแหน่ง
            $table->decimal('total_carbon_saved', 12, 4)
                  ->default(0) // สำคัญ! ต้องมีค่าเริ่มต้นเป็น 0 เพื่อไม่ให้ Error กับข้อมูลเก่า
                  ->after('total_points')
                  ->comment('ยอดรวมคาร์บอนที่ลดได้ทั้งบิล (kgCO2e)');
        });
    }

    public function down(): void
    {
        Schema::table('kp_purchase_transactions', function (Blueprint $table) {
            $table->dropColumn('total_carbon_saved');
        });
    }
};
