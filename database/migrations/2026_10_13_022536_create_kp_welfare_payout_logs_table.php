<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('welfare_payout_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payout_id')->constrained('welfare_payouts')->onDelete('cascade');
            $table->foreignId('step_id')->nullable()->constrained('approval_workflow_steps')->onDelete('set null');
            $table->foreignId('action_by_user_id')->constrained('users')->onDelete('cascade');
            $table->enum('status', ['submitted', 'approved', 'rejected', 'returned']);
            $table->string('attachment_path')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('welfare_payout_logs');
    }
};