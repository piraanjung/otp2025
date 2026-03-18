<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('foodwaste_transactions', function (Blueprint $table) {
            // เพิ่ม waste_log_id (ให้เป็น nullable เพราะถ้าเป็นรายการ redeem หรือ buy_compost จะไม่มี log ขยะ)
            $table->unsignedBigInteger('waste_log_id')->nullable()->after('fw_pref_id_fk');
        });
    }

    public function down()
    {
        Schema::table('foodwaste_transactions', function (Blueprint $table) {
            $table->dropColumn('waste_log_id');
        });
    }
};
