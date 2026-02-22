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
        Schema::create('emission_factors', function (Blueprint $table) {
            $table->id();
            $table->string('material_name'); // เช่น Plastics: PET (incl. forming)
            $table->string('unit')->default('kg'); // หน่วยพื้นฐานในระบบคือ kg
            $table->decimal('ef_value', 10, 5); // ค่า EF ต่อหน่วย
            $table->string('source')->nullable(); // แหล่งที่มา เช่น DEFRA 2025
            $table->text('example')->nullable(); // แหล่งที่มา เช่น DEFRA 2025
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('emission_factors');
    }
};
