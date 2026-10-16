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
    Schema::create('kiosk_sensors', function (Blueprint $table) {
    $table->id();
    $table->string('sensor_code', 50); // เช่น ULTRASONIC_BIN, IR_BOTTLE, SERVO_DOOR, CAM_AI
    $table->string('sensor_name', 100); // ชื่อแสดงผล เช่น เซ็นเซอร์ถังขยะเต็ม, กล้อง AI
    $table->enum('status', ['ok', 'warning', 'error', 'offline'])->default('ok');
    $table->string('last_val')->nullable(); // ค่าที่อ่านได้ล่าสุด เช่น 85%, NORMAL
    $table->timestamp('last_ping_at')->nullable();
    $table->timestamps();

    $table->foreign('kiosk_id')->references('id')->on('kiosks')->onDelete('cascade');
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kiosk_sensors');
    }
};
