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
        // เพิ่ม Column เพื่อเก็บว่าเบิกจากขวด ID ไหน
        $table->unsignedBigInteger('inv_item_detail_id_fk')->after('inv_item_id_fk')->nullable();
    });
}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('inv_transactions', function (Blueprint $table) {
            //
        });
    }
};
