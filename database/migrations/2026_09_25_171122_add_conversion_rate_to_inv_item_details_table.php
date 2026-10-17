<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('inv_item_details', function (Blueprint $table) {
        $table->decimal('conversion_rate', 10, 2)->default(1)->after('location_id_fk'); // อัตราส่วนแปลงหน่วย
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inv_item_details', function (Blueprint $table) {
            //
        });
    }
};
