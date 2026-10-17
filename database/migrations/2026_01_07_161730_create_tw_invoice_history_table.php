<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
       // วิธีที่ง่ายที่สุดสำหรับ MySQL (Copy โครงสร้างรวม Index)
        DB::statement('CREATE TABLE tw_invoice_history LIKE tw_invoice');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tw_invoice_history');
    }
};
