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
        Schema::create('user_profile', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->string('name');
            $table->string('id_card');
            $table->string('phone');
            $table->enum('gender',['m', 'w']);
            $table->string('address');
            $table->integer('zone_id')->comment('หมู่หรือชุมชน แล้วแต่พื้นที่จะแยก');
            $table->integer('subzone_id')->default(0);
            $table->string('tambon_code');
            $table->string('district_code');
            $table->string('province_code');
            $table->enum('status',['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_profile');
    }
};
