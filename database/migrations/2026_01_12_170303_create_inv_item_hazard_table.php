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
    Schema::create('inv_item_hazard', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('inv_item_id');
        $table->unsignedBigInteger('inv_hazard_level_id');
        
        // FK Constraints
        $table->foreign('inv_item_id')->references('id')->on('inv_items')->onDelete('cascade');
        $table->foreign('inv_hazard_level_id')->references('id')->on('inv_hazard_levels')->onDelete('cascade');
    });
}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inv_item_hazard');
    }
};
