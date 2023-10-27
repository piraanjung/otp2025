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
        Schema::create('zones', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('zone_name');
            $table->text('location');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->string('tambon_code');
            $table->timestamps();
            // $table->foreign('tambon_id')->references('tambon_code')->on('tambons')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('zones');
    }
};
