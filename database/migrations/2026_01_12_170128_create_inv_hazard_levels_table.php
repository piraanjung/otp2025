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
    Schema::create('inv_hazard_levels', function (Blueprint $table) {
        $table->id();
        
        // ผูกกับ Org
        $table->unsignedBigInteger('org_id_fk');
        $table->foreign('org_id_fk')->references('id')->on('organizations')->onDelete('cascade');

        $table->string('name');             // ชื่อระดับ เช่น "สารไวไฟ", "กัดกร่อน"
        $table->string('code')->nullable(); // รหัสสากล เช่น "GHS02"
        $table->text('description')->nullable(); // รายละเอียด
        $table->string('image_path')->nullable(); // เก็บ path รูปไอคอน
        
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
        Schema::dropIfExists('inv_hazard_levels');
    }
};
