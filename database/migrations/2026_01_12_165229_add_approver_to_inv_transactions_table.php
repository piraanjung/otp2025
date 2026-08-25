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
    Schema::table('inv_transactions', function (Blueprint $table) {
        // เพิ่มชื่อผู้เบิก (เผื่อคีย์แทนคนอื่น) และ ชื่อผู้อนุมัติ
        $table->string('requester_name')->after('user_id_fk')->nullable(); 
        $table->string('approver_name')->after('requester_name')->nullable();
    });
}

public function down()
{
    Schema::table('inv_transactions', function (Blueprint $table) {
        $table->dropColumn(['requester_name', 'approver_name']);
    });
}
};
