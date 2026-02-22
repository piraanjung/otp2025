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
        Schema::create('tw_meter_type_rate_configs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meter_type_id_fk')->constrained('tw_meter_types')->onDelete('cascade');
            $table->foreignId('pricing_type_id')->constrained('tw_pricing_types')->onDelete('restrict'); // Fixed or Progressive

            $table->float('min_usage_charge', 8, 2)->nullable(); // ค่ารักษามิเตอร์/ค่าธรรมเนียมขั้นต่ำ (สำหรับ Fixed Rate)
            $table->float('vat', 4, 2)->default(0); // ค่ารักษามิเตอร์/ค่าธรรมเนียมขั้นต่ำ (สำหรับ Fixed Rate)
            $table->float('fixed_rate_per_unit', 8, 2)->nullable(); // อัตราต่อหน่วย (สำหรับ Fixed Rate)

            $table->date('effective_date'); // วันที่เริ่มมีผล
            $table->date('end_date')->nullable(); // วันที่สิ้นสุด (ถ้ามี)
            $table->boolean('is_active')->default(true); // สถานะใช้งาน
            $table->text('comment')->nullable();
            $table->timestamps();

            // $table->unique(['metertype_id', 'effective_date'], 'meter_type_effective_date_unique'); // ห้ามมี config ซ้ำสำหรับ MeterType เดียวกันในวันเดียวกัน
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meter_type_rate_configs');
    }
};
