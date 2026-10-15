<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tabwater_quality_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('org_id_fk');
            $table->unsignedBigInteger('station_id')->comment('FK -> tabwater_stations.id');
            $table->enum('data_source', ['manual', 'iot'])->default('manual')->comment('ที่มาของข้อมูล');
            $table->unsignedBigInteger('staff_id')->nullable()->comment('FK -> staffs.id');
            $table->string('device_mac', 50)->nullable()->comment('MAC Address กล่อง IoT');

            // 1. ค่าน้ำจากการวัด/เซ็นเซอร์
            $table->decimal('secchi_depth_cm', 5, 2)->nullable()->comment('ระยะไม้จุ่ม (cm)');
            $table->decimal('turbidity_ntu', 5, 2)->nullable()->comment('ค่าความขุ่น (NTU)');
            $table->decimal('tds_ppm', 6, 2)->nullable()->comment('สารละลายรวม (TDS PPM)');
            $table->decimal('ph_value', 3, 1)->nullable()->comment('ค่า pH');
            $table->string('ph_litmus_image')->nullable()->comment('รูปถ่ายกระดาษลิตมัส');
            $table->decimal('water_temp_c', 4, 1)->nullable()->comment('อุณหภูมิน้ำ (°C)');
            $table->decimal('water_level_percent', 5, 2)->nullable()->comment('ระดับน้ำใสในถังพัก (%)');

            // 2. ข้อมูลการเติมสารเคมีหน้างานจริง
            $table->enum('alum_valve_level', ['low', 'medium', 'high'])->nullable()->comment('ระดับการเปิดก๊อกสารส้ม');
            $table->integer('lime_bucket_count')->nullable()->comment('จำนวนถังน้ำปูนขาวที่ผสม');
            $table->decimal('alum_used_kg', 8, 2)->default(0)->comment('สารส้มที่ใส่จริง (kg)');
            $table->decimal('lime_used_kg', 8, 2)->default(0)->comment('ปูนขาวที่ใส่จริง (kg)');

            $table->date('log_date')->comment('วันที่บันทึก');
            $table->time('log_time')->comment('เวลาที่บันทึก');
            $table->text('remark')->nullable();
            $table->timestamps();

            $table->foreign('station_id')->references('id')->on('tabwater_stations')->onDelete('cascade');
            $table->foreign('staff_id')->references('id')->on('staffs')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tabwater_quality_logs');
    }
};