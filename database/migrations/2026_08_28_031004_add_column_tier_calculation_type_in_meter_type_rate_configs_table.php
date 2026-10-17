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
           // 1: Progressive Rate (ขั้นบันไดสะสม - ซอยคิดทีละช่วง)
            // 2: Flat Bracket Rate (เหมาเรทตามช่วง - เอาหน่วยทั้งหมด x อัตราของช่วงนั้น)
            $table->tinyInteger('tier_calculation_type')->default(1)->comment('Progressive =>1=คิดเงินซอยคิดทีละช่วง, 2เอาหน่วยน้ำ x อัตราของช่วงนั้น')->after('pricing_type_id');
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
