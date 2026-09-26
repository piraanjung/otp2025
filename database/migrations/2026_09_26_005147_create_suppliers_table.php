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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('org_id_fk'); // แยกตามองค์กร (Multi-tenant)
            $table->string('department'); // แยกตามองค์กร (Multi-tenant)
            $table->string('name');                  // ชื่อร้านค้า / บริษัท
            $table->string('contact_person')->nullable(); // ชื่อผู้ติดต่อ
            $table->string('phone')->nullable();     // เบอร์โทรศัพท์
            $table->text('address')->nullable();     // ที่อยู่
            $table->string('tax_number')->nullable(); // เลขประจำตัวผู้เสียภาษี (ถ้ามี)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
