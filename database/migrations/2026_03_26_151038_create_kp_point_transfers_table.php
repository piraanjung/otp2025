<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('kp_point_transfers', function (Blueprint $table) {
        $table->id();
        $table->foreignId('sender_id')->constrained('users'); // ผู้โอน
        $table->foreignId('receiver_id')->constrained('users'); // ผู้รับ
        $table->integer('amount'); // จำนวนแต้ม
        $table->string('note')->nullable(); // บันทึกช่วยจำ
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kp_point_transfers');
    }
};
