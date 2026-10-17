<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('foodwaste_issue_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('org_id_fk')->nullable()->constrained('organizations')->onDelete('set null');
            $table->string('name'); // ชื่อหมวดหมู่ เช่น "ปัญหาการใช้งานแอป", "ปัญหาถังหมัก"
            $table->boolean('is_active')->default(true); // เปิด/ปิดการใช้งาน
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('foodwaste_issue_types');
    }
};
