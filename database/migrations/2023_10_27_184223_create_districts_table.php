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
        Schema::create('districts', function (Blueprint $table) {
            $table->string('district_code');
            $table->string('district_name');
            $table->string('province_id');
            $table->integer('go_id')->default(0);
            $table->timestamps();
            $table->primary('district_code');
            // $table->foreign('province_id')->references('province_code')->on('provinces')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('districts');
    }
};
