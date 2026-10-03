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
        Schema::create('approval_workflow_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workflow_id')->constrained('approval_workflows')->onDelete('cascade');
            $table->integer('step_order'); // ลำดับที่ 1, 2, 3...
            $table->string('role_name')->nullable(); // หรือเก็บ role_id ตามระบบที่คุณใช้
            $table->unsignedBigInteger('specific_user_id')->nullable(); // กรณีระบุตัวบุคคลเฉพาะเจาะจง
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approval_workflow_steps');
    }
};
