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
        Schema::create('foodwaste_user_bin_iotbox_matchings', function (Blueprint $table) {
            $table->id();

            // 1. fw_user_id_fk: เชื่อมโยงกับ table foodwaste_user_preferences
            $table->foreignId('fw_user_id_fk')->constrained('foodwaste_user_preferences')->onDelete('cascade');

            // 2. bin_id_fk: เชื่อมโยงกับ table foodwaste_bins
            $table->foreignId('bin_id_fk')->constrained('foodwaste_bins')->onDelete('cascade');

            // 3. iotbox_id_fk: เชื่อมโยงกับ table foodwast_iotboxes
            $table->foreignId('iotbox_id_fk')->constrained('foodwast_iotboxes')->onDelete('cascade');

            // เพื่อให้แน่ใจว่าการจับคู่ในแต่ละคอลัมน์ไม่ซ้ำกัน
            $table->unique(['fw_user_id_fk', 'bin_id_fk', 'iotbox_id_fk'], 'unique_matching_combination');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('foodwaste_user_bin_iotbox_matchings');
    }
};