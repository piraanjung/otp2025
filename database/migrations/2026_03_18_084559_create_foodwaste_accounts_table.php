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
        Schema::create('foodwaste_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fw_pref_id_fk')->constrained('foodwaste_user_preferences');
            $table->integer('points_balance')->default(0);      // แต้มสะสมปัจจุบัน
            $table->decimal('money_balance', 10, 2)->default(0); // ยอดเงินปันผล (ถ้ามี)
            $table->decimal('total_weight_contributed', 10, 2)->default(0); // น้ำหนักขยะรวมที่เคยส่ง (ไว้คิดสัดส่วนปันผล)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('foodwaste_accounts');
    }
};
