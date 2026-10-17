<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kp_money_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('org_id_fk')->constrained('organizations');
            
            // เติม ->nullable() เพื่อให้คอลัมน์รองรับค่า NULL ตอนทำ onDelete('set null')
            $table->foreignId('batch_id_fk')->nullable()->constrained('kp_withdraw_batches')->onDelete('set null');
            
            $table->float('amount', 53);
            $table->string('verification_code', 6);
            $table->string('status');
            $table->enum('hold_status', ['held', 'released', 'settled'])->default('held');
            $table->tinyInteger('is_proxy')->default(0);
            $table->string('proxy_name')->nullable();
            $table->string('proxy_id_card', 20)->nullable();
            $table->string('proxy_relationship', 100)->nullable();
            $table->date('payout_date');
            
            // แนะนำใส่ ->nullable() ให้ admin_id, completed_at, payout_note หากข้อมูลเริ่มต้นอาจยังไม่มีค่า
            $table->foreignId('admin_id')->nullable()->constrained('users')->onDelete('set null');
            $table->dateTime('completed_at')->nullable();
            $table->string('signature_path')->nullable();
            $table->enum('payout_method', ['cash', 'transfer'])->default('cash');
            $table->text('payout_note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kp_money_requests');
    }
};