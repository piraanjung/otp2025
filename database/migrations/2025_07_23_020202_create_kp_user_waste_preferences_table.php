<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKpUserWastePreferencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kp_user_waste_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Foreign key ไปยังตาราง users
            $table->boolean('is_annual_collection')->default(false); // เป็นสมาชิกเก็บขยะรายปีหรือไม่
            $table->boolean('is_waste_bank')->default(false);        // เป็นสมาชิกธนาคารขยะหรือไม่
            $table->timestamps();
            $table->unique('user_id'); // User 1 คน มีได้แค่ 1 preference record
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('kp_user_waste_preferences');
    }
}