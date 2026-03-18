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
        Schema::create('foodwaste_issue_reports', function (Blueprint $table) {
            $table->id();

            // 1. เพิ่ม nullable() ก่อน constrained() เสมอเมื่อใช้ onDelete('set null')
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('batch_id')->nullable()->constrained('compost_batches')->onDelete('set null');
            $table->foreignId('issue_type_id')->nullable()->constrained('foodwaste_issue_types')->onDelete('set null');

            $table->string('description')->nullable();

            // 2. แยก Single Quote ของ enum ให้ถูกต้องทีละตัว
            $table->enum('status', ['pending', 'in_progress', 'resolved', 'cancel'])->default('pending');

            $table->string('admin_note')->nullable();
            $table->string('staff_comment')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('foodwaste_issue_reports');
    }
};
