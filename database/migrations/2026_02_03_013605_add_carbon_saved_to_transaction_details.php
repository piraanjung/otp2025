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
        Schema::table('kp_purchase_transactions_details', function (Blueprint $table) {
            // เพิ่ม column carbon_saved ทศนิยม 4 ตำแหน่ง
            $table->decimal('carbon_saved', 12, 4)->default(0)->after('points')->comment('ปริมาณคาร์บอนที่ลดได้ (kgCO2e)');
        });
    }

    public function down()
    {
        Schema::table('kp_purchase_transactions_details', function (Blueprint $table) {
            $table->dropColumn('carbon_saved');
        });
    }
};
