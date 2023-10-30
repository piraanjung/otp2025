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
        Schema::create('tambons', function (Blueprint $table) {
            $table->unsignedBigInteger('id');
            $table->string('tambon_name');
            $table->unsignedBigInteger('district_id');
            $table->string('zipcode');
            $table->timestamps();
            $table->primary(['id', 'district_id']);
            $table->foreign('district_id')->references('id')->on('districts')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tambons');
    }
};
