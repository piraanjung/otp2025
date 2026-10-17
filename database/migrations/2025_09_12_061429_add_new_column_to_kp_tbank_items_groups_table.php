<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('kp_tbank_items_groups', function (Blueprint $table) {
            $table->string('kp_items_group_code')->after('kp_items_groupname');
            $table->integer('sequence_number')->default(1);
        });
    }

    public function down()
    {
        Schema::table('kp_tbank_items_groups', function (Blueprint $table) {
            // แก้ไขเป็น sequence_number ให้ตรงกับฟังก์ชัน up()
            // หรือลบพร้อมกันด้วย Array: $table->dropColumn(['kp_items_group_code', 'sequence_number']);
            if (Schema::hasColumn('kp_tbank_items_groups', 'kp_items_group_code')) {
                $table->dropColumn('kp_items_group_code');
            }
            if (Schema::hasColumn('kp_tbank_items_groups', 'sequence_number')) {
                $table->dropColumn('sequence_number');
            }
        });
    }
};