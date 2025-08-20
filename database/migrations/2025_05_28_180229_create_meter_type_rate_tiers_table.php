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
        Schema::create('tw_meter_type_rate_tiers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meter_type_rate_config_id')->constrained('tw_meter_type_rate_configs')->onDelete('cascade');
            
            $table->integer('min_units'); // หน่วยขั้นต่ำของช่วง
            $table->integer('max_units')->nullable(); // หน่วยสูงสุดของช่วง (nullable สำหรับช่วงสุดท้าย)
            $table->float('rate_per_unit', 8, 2); // อัตราต่อหน่วยในแต่ละช่วง
            $table->integer('tier_order'); // ลำดับของช่วง (1, 2, 3...)
            $table->text('comment')->nullable();
            $table->timestamps();

            $table->unique(['meter_type_rate_config_id', 'tier_order'], 'tier_order_unique_per_config');
            // หรือ $table->unique(['meter_type_rate_config_id', 'min_units'], 'min_units_unique_per_config');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tw_meter_type_rate_tiers');
    }
};
