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
        Schema::create('foodwast_iotboxes', function (Blueprint $table) {
            $table->id()->autoIncrement(); // id int(11) primary key
            $table->string('iotbox_code', 100)->unique(); // iotbox_code varchar(50)
            // สำหรับ enum('0','1') ใน Laravel ควรใช้ $table->boolean() หรือ $table->enum()
            $table->enum('temp_humid_sensor', ['0', '1']);
            $table->enum('gas_sensor', ['0', '1']);
            $table->enum('weight_sensor', ['0', '1']);
            $table->timestamps(); // สร้าง created_at และ updated_at datetime อัตโนมัติ
            // หากคุณต้องการ updated_at เพียงอย่างเดียว ให้ใช้ $table->timestamp('updated_at')->nullable(); แทน $table->timestamps(); แต่แนะนำให้ใช้ timestamps()
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('foodwast_iotboxes');
    }
};