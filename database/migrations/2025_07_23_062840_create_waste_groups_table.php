<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWasteGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kp_waste_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // ชื่อกลุ่ม เช่น "พลาสติก", "แก้ว", "กระดาษ"
            $table->text('description')->nullable(); // คำอธิบายกลุ่ม
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
        Schema::dropIfExists('kp_waste_groups');
    }
}
