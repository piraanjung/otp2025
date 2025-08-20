<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cp_sensor_data', function (Blueprint $table) {
        $table->id();
        $table->decimal('temperature', 8, 2); // อุณหภูมิ
        $table->decimal('humidity', 8, 2);    // ความชื้น
        $table->decimal('methane_gas', 8, 2); // ก๊าซมีเทน
        $table->decimal('weight', 8, 2);      // น้ำหนัก
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sensor_data');
    }
};
