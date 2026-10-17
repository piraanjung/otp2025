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
    Schema::create('inv_categories', function (Blueprint $table) {
        $table->id();
        
        // ✅ ใช้ org_id_fk เชื่อมกับตาราง organizations ที่มีอยู่แล้ว
        $table->unsignedBigInteger('org_id_fk');
        
        $table->foreign('org_id_fk')->references('id')->on('organizations')->onDelete('cascade');

        $table->string('name'); // ชื่อหมวดหมู่
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inv_categories');
    }
};
