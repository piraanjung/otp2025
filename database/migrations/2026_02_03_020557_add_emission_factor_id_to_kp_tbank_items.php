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
            // 1. ✅ สร้าง Column ขึ้นมาก่อน (ต้องเป็น unsignedBigInteger และ nullable เพราะเราใช้ set null)
            $table->unsignedBigInteger('ef_id_fk')->nullable()->after('org_id_fk'); // ใส่ after เพื่อจัดตำแหน่ง (Option)

            // 2. ✅ จากนั้นค่อยผูก Foreign Key
            $table->foreign('ef_id_fk')
                ->references('id')
                ->on('emission_factors')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kp_tbank_items', function (Blueprint $table) {
            // เวลาลบ ควรลบ Foreign Key ออกก่อน แล้วค่อยลบ Column
            $table->dropForeign(['ef_id_fk']);
            $table->dropColumn('ef_id_fk');
        });
    }
};
