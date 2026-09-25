<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inv_transaction_approvals', function (Blueprint $table) {
            $table->id();
            $table->string('ref_no'); // เชื่อมโยงกับใบเบิก
            $table->integer('step_order')->default(1); // ลำดับขั้นการอนุมัติ (1, 2, 3...)
            $table->unsignedBigInteger('approver_id'); // ผู้ออนุมัติ (User ID)
            $table->enum('status', ['PENDING', 'APPROVED', 'REJECTED'])->default('PENDING');
            $table->text('comment')->nullable(); // ความเห็นเพิ่มเติม
            $table->timestamp('action_at')->nullable(); // วันเวลาที่ทำรายการ
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inv_transaction_approvals');
    }
};