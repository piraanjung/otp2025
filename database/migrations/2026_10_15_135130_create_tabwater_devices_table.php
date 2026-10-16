<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tabwater_devices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('station_id')->comment('FK -> tabwater_stations.id');
            $table->string('device_code', 50)->comment('รหัสอุปกรณ์ เช่น PUMP-RAW-01');
            $table->string('name')->comment('ชื่ออุปกรณ์ เช่น ปั๊มน้ำดิบ 5 กม., วาล์วถังใส');
            $table->enum('type', ['pump_raw', 'pump_clear', 'dosing_pump', 'electric_valve', 'relay'])->default('pump_raw');
            
            // สถานะและการควบคุม
            $table->boolean('status')->default(false)->comment('สถานะปัจจุบัน (0=ปิด, 1=เปิด)');
            $table->enum('control_mode', ['auto', 'manual'])->default('auto')->comment('โหมด Auto หรือ Manual');
            $table->boolean('auto_control_enabled')->default(true)->comment('เปิด/ปิดการตัดอัตโนมัติ');
            
            $table->string('mqtt_topic')->nullable()->comment('Topic สำหรับสั่งงาน IoT');
            $table->integer('relay_channel')->nullable()->comment('ช่อง Relay');

            $table->timestamps();

            $table->foreign('station_id')->references('id')->on('tabwater_stations')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tabwater_devices');
    }
};