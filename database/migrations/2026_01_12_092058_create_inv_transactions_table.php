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
        $table->unsignedBigInteger('org_id_fk');
        
        // User คนเบิก (ผูกกับตาราง users ปกติ)
        $table->unsignedBigInteger('requester_id_fk')->on('users')->onDelete('cascade');
        $table->unsignedBigInteger('approve_workflow_id_fk')->on('approval_workflows')->onDelete('cascade');
          
        // พัสดุที่เบิก
        $table->foreign('inv_item_id_fk')->references('id')->on('inv_items');
        
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
