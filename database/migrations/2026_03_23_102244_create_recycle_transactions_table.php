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
        Schema::create('recycle_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->enum('type', ['deposit', 'withdraw']); // ฝาก(ขายขยะ) หรือ ถอน(เบิกเงิน)
            $table->decimal('amount', 10, 2);
            $table->decimal('weight_kg', 10, 2)->default(0); // เก็บน้ำหนักไว้เช็คเกณฑ์ Waive
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recycle_transactions');
    }
};
