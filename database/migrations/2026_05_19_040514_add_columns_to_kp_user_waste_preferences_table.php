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
        Schema::table('kp_user_waste_preferences', function (Blueprint $table) {

            // เพิ่มคอลัมน์ที่อยู่และรหัสการปกครอง โดยต่อหลังคอลัมน์is_waste_bank
            $table->text('address')->nullable()->after('is_recycle_bank')->comment('ที่อยู่เฉพาะประจำองค์กร');
            $table->unsignedBigInteger('tambon_code')->nullable()->after('address');
            $table->unsignedBigInteger('district_code')->nullable()->after('tambon_code');
            $table->unsignedBigInteger('province_code')->nullable()->after('district_code');
            $table->unsignedBigInteger('zone_id')->nullable()->after('province_code');
            $table->unsignedBigInteger('subzone_id')->nullable()->after('zone_id');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kp_user_waste_preferences', function (Blueprint $table) {

            // ลบคอลัมน์ทั้งหมดที่เพิ่มเข้าไป
            $table->dropColumn([
                'address',
                'tambon_code',
                'district_code',
                'province_code',
                'zone_id',
                'subzone_id'
            ]);
        });
    }
};
