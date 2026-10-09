<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('kp_withdraw_batches', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('org_id_fk');
            $table->string('batch_no', 50)->unique();
            $table->date('cutoff_date');
            $table->date('payout_date');
            $table->integer('total_requests')->default(0);
            $table->decimal('total_amount', 12, 2)->default(0.00);
            $table->enum('status', ['draft', 'in_review', 'approved', 'rejected', 'completed'])->default('draft');
            $table->integer('current_step_order')->default(1);
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            $table->foreign('org_id_fk')->references('id')->on('organizations')->onDelete('cascade');
        });
    }

    public function down(): void {
        Schema::dropIfExists('kp_withdraw_batches');
    }
};