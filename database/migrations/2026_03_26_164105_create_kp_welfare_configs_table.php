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
        Schema::create('kp_welfare_configs', function (Blueprint $table) {
    $table->id();
    $table->foreignId('org_id_fk')->constrained('organizations');
    $table->integer('min_months_active')->default(6); // ต้องเป็นสมาชิกมาแล้วกี่เดือน
    $table->integer('min_total_weight')->default(50); // ต้องส่งขยะรวมกี่กิโลกรัม
    $table->decimal('default_payout_amount', 12, 2)->default(5000); // ยอดเงินมาตรฐาน
    $table->text('criteria_description')->nullable(); // คำอธิบายเกณฑ์
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kp_welfare_configs');
    }
};
