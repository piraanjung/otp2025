<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up() {
    Schema::create('foodwaste_meal_items', function (Blueprint $table) {
        $table->id();
        $table->foreignId('meal_log_id')->constrained('foodwast_meal_logs')->onDelete('cascade');
        $table->string('menu_name');
        $table->string('category')->nullable();
        $table->integer('calories')->default(0);
        $table->enum('status', ['verified', 'pending_review'])->default('verified');
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meal_items');
    }
};
