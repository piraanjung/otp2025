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
        Schema::table('inv_transaction_approvals', function (Blueprint $table) {
            if (!Schema::hasColumn('inv_transaction_approvals', 'module_name')) {
                // เช่น 'kept_kaya_withdraw', 'inventory', 'welfare'
                $table->string('module_name', 50)->default('inventory')->after('ref_no');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inv_transaction_approvals', function (Blueprint $table) {
            $table->removeColumn('module_name');
        });
    }
};
