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
            $table->string('tambon_code');
            $table->string('tambon_name');
            $table->string('district_code');
            $table->string('zipcode');
            $table->timestamps();
            $table->primary('tambon_code');
            // $table->foreign('district_code')->references('district_code')->on('districts')->onDelete('cascade');

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
