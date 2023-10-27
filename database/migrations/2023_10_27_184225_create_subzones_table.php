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
        Schema::create('subzones', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('zone_id');
            $table->string('subzone_name');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->foreign('zone_id')->references('id')->on('zones')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('subzones');
    }
};
