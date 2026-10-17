<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        // 1. ตารางเก็บข้อมูลก่อนกิน (วิเคราะห์ด้วย AI)
        Schema::create('foodwast_meal_logs', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained();
        $table->string('photo_path');
        $table->integer('total_calories')->default(0);
        $table->timestamps();
    });

        // 2. ตารางเก็บข้อมูลขยะและคาร์บอนเครดิต
        Schema::create('foodwaste_waste_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('weight_kg', 8, 2);
            $table->string('photo_path')->nullable();
            $table->boolean('is_mixed')->default(false); // กวนขยะหรือยัง
            $table->enum('moisture', ['dry', 'good', 'wet']);
            $table->decimal('carbon_saved_kg', 8, 4); // คาร์บอนที่ลดได้
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('foodwaste_waste_logs');
        Schema::dropIfExists('foodwast_meal_logs');
    }
};
