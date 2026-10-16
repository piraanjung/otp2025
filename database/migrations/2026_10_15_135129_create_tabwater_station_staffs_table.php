<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tabwater_station_staffs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('station_id')->comment('FK -> tabwater_stations.id');
            $table->unsignedBigInteger('staff_id')->comment('FK -> staffs.id');
            $table->enum('role', ['primary', 'secondary'])->default('secondary')->comment('ระดับการรับผิดชอบ หลัก/รอง');
            $table->timestamps();

            $table->foreign('station_id')->references('id')->on('tabwater_stations')->onDelete('cascade');
            $table->foreign('staff_id')->references('id')->on('staffs')->onDelete('cascade');
            $table->unique(['station_id', 'staff_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tabwater_station_staffs');
    }
};