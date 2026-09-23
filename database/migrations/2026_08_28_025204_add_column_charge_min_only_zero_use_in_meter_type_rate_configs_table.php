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
        Schema::table('tw_meter_type_rate_configs', function (Blueprint $table) {
            // 0 = คิดรวมกับค่าน้ำเสมอ (Always charge)
            // 1 = คิดเฉพาะเมื่อใช้น้ำ 0 หน่วย (Only charge when zero water used)
            $table->tinyInteger('charge_min_only_zero_use')->default(0)->comment(' 0 = คิดรวมกับค่าน้ำเสมอ, 1 = คิดเฉพาะเมื่อใช้น้ำ 0 หน่วย ')->after('min_usage_charge');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('meter_type_rate_configs', function (Blueprint $table) {
            //
        });
    }
};
