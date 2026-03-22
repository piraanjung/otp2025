<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('foodwaste_issue_reports', function (Blueprint $table) {
            $table->unsignedBigInteger('assigned_staff_id')->nullable()->after('id');

            // 2. เมื่อมีคอลัมน์แล้ว ถึงจะสั่งทำ Foreign Key ได้
            $table->foreign('assigned_staff_id')
                ->references('user_id')
                ->on('staffs')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('foodwaste_issue_reports', function (Blueprint $table) {
            $table->dropForeign(['assigned_staff_id']);
            $table->dropColumn('assigned_staff_id');
        });
    }
};
