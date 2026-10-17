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
        Schema::create('foodwaste_compost_batches', function (Blueprint $table) {
            $table->id();
    $table->string('batch_code'); // เช่น LOT-6701-001
    $table->date('start_date');
    $table->date('closed_date')->nullable(); // วันที่หยุดเติมขยะ
    $table->enum('status', ['filling', 'composting', 'ready'])->default('filling');
    $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('foodwaste_compost_batches');
    }
};
