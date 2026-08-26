<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tw_notifies', function (Blueprint $table) {
            // เพิ่ม system_type ต่อท้าย user_id
            $table->string('system_type')->default('water')->after('user_id');
            
            // เพิ่ม custom_issue_type สำหรับเก็บข้อความกรณีเลือก "อื่นๆ" ต่อท้าย issue_type
            $table->string('custom_issue_type')->nullable()->after('issue_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tw_notifies', function (Blueprint $table) {
            $table->dropColumn(['system_type', 'custom_issue_type']);
        });
    }
};