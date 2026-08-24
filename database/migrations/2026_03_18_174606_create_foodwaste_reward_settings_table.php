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
        Schema::create('foodwaste_reward_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();      // เช่น 'daily_base_pts', 'evening_bonus_pts'
            $table->string('title');             // ชื่อที่โชว์ในหน้า Admin
            $table->decimal('value', 10, 2);     // ค่าตัวเลข (แต้ม/เงิน/เวลา)
            $table->string('unit');              // pts, thb, time
            $table->string('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('foodwaste_reward_settings');
    }
};
