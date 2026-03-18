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
    Schema::table('waste_logs', function (Blueprint $table) {
        // เพิ่มคอลัมน์สำหรับเก็บระดับความร้อน (อุ่น, ร้อน, เย็น)
        $table->string('temperature_feel')->nullable()->after('moisture');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('waste_logs', function (Blueprint $table) {
            $table->removeColumn('temperature_feel');
        });
    }
};
