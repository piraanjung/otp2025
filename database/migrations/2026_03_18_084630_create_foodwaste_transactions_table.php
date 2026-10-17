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
        Schema::create('foodwaste_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fw_pref_id_fk')->constrained('foodwaste_user_preferences');
            $table->string('transaction_type'); // 'earn' (ได้แต้ม), 'redeem' (แลกของ), 'discount' (ใช้เป็นส่วนลดปุ๋ย), 'dividend' (ปันผลเงิน)
            $table->integer('points')->default(0);
            $table->decimal('amount', 10, 2)->default(0);
            $table->string('note')->nullable(); // เช่น "ทิ้งขยะ 5kg", "แลกไข่ไก่ 1 แผง"
            $table->foreignId('staff_id')->constrained('users'); // ใครเป็นคนทำรายการ
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('foodwaste_transactions');
    }
};
