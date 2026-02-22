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
        Schema::create('kiosk_matches', function (Blueprint $table) {
        $table->id();
        $table->string('kiosk_id')->index(); // รหัสตู้
        $table->unsignedBigInteger('user_id'); // ID ของคุณพิพัฒน์พงษ์
        $table->enum('status', ['pending', 'active', 'completed', 'expired'])->default('pending');
        $table->timestamp('expires_at');
        $table->timestamps();

        // Foreign key (ถ้ามีตาราง users/user_waste_prefs)
        $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kiosk_matches');
    }
};
