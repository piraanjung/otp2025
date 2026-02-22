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
        Schema::create('foodwaste_bins', function (Blueprint $table) {
            $table->id()->autoIncrement();
            $table->string('bin_code', 191)->unique(); 
            $table->string('bin_type', 191);
            $table->string('location_description', 191)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->enum('status', ['active', 'inactive', 'damaged', 'removed'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('foodwaste_bins');
    }
};