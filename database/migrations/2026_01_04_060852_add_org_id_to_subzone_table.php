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
        Schema::table('subzones', function (Blueprint $table) {
           $table->unsignedBigInteger('org_id_fk')->after('id')->nullable(); 
        
        $table->foreign('org_id_fk')->references('id')->on('organizations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('subzones', function (Blueprint $table) {
             $table->dropForeign(['org_id_fk']); // ถ้าสร้าง FK ไว้ต้องลบก่อน
        $table->dropColumn('org_id_fk');
        });
    }
};
