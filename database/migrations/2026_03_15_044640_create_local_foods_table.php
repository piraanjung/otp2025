<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('local_foods', function (Blueprint $table) {
            $table->id();
            $table->string('category'); // หมวดหมู่ เช่น ต้ม, ผัด, แกง
            $table->string('menu_name')->unique(); // ชื่อเมนู
            $table->integer('calories'); // แคลอรี่
            $table->timestamps();
        });

        // แอบใส่ข้อมูลตัวอย่าง (Seeder) ไว้ทดสอบระบบ
        DB::table('local_foods')->insert([
            ['category' => 'ผัด/ทอด', 'menu_name' => 'ผัดกะเพราหมูสับ', 'calories' => 550],
            ['category' => 'ต้ม/แกง', 'menu_name' => 'แกงอ่อมไก่', 'calories' => 150],
            ['category' => 'ต้ม/แกง', 'menu_name' => 'แกงหน่อไม้', 'calories' => 120],
        ]);
    }

    public function down() {
        Schema::dropIfExists('local_foods');
    }
};
