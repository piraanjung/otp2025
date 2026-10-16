<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tabwater_device_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('device_id')->comment('FK -> tabwater_devices.id');
            $table->unsignedBigInteger('staff_id')->nullable()->comment('ผู้สั่งงาน (NULL กรณี Auto System สั่ง)');
            
            $table->enum('action', ['ON', 'OFF'])->comment('คำสั่งเปิด หรือ ปิด');
            $table->enum('mode', ['auto', 'manual'])->default('manual')->comment('สั่งด้วยโหมด Auto หรือ Manual');
            $table->string('trigger_reason')->comment('สาเหตุ เช่น ระดับน้ำเต็ม 95% (Auto), นายสมชาย สั่งปิด (Manual)');
            
            $table->timestamps();

            $table->foreign('device_id')->references('id')->on('tabwater_devices')->onDelete('cascade');
            $table->foreign('staff_id')->references('id')->on('staffs')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tabwater_device_logs');
    }
};