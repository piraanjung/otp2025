<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::create('inv_transactions', function (Blueprint $table) {
        $table->id();

        // ✅ เชื่อม org
        $table->foreignId('org_id_fk')->constrained('organizations')->onDelete('cascade');
        
        // User คนเบิก (ผูกกับตาราง users ปกติ)
        $table->foreignId('requester_id_fk')->constrained('users')->onDelete('cascade');
        $table->foreignId('approve_workflow_id_fk')->on('approval_workflows')->onDelete('cascade');
          
        // พัสดุที่เบิก
        $table->foreignId('inv_item_id_fk')->constrained('inv_items')->onDelete('cascade');
        
        $table->integer('quantity'); 
        $table->string('purpose')->nullable(); 
        
        // --- Workflow ---
        $table->enum('status', ['PENDING', 'APPROVED', 'REJECTED', 'COMPLETED', 'RETURNED'])->default('PENDING');
        $table->integer('current_step')->default(1);
        
        $table->timestamp('transaction_date')->useCurrent();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inv_transactions');
    }
};
