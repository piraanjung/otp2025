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
        Schema::create('users', function (Blueprint $table) {
            $table->unsignedBigInteger('id');
            $table->string('username');
            $table->string('password');
            $table->string('prefix');
            $table->integer('role_id');
            $table->string('firstname');
            $table->string('lastname');
            $table->string('email')->unique();
            $table->string('line_id')->nullable();
            $table->string('id_card');
            $table->string('phone');
            $table->enum('gender',['m', 'w']);
            $table->string('address');
            $table->integer('zone_id')->comment('หมู่หรือชุมชน แล้วแต่พื้นที่จะแยก');
            $table->integer('subzone_id')->default(0);
            $table->string('tambon_code');
            $table->string('district_code');
            $table->string('province_code');
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->enum('status', ['active', 'inactive', 'deleted'])->default('active');
            $table->timestamps();
            $table->primary('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
