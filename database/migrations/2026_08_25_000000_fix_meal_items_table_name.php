<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The model and controllers query the `meal_items` table, but an earlier
     * migration created it as `foodwaste_meal_items`. Reconcile the name:
     * rename the mis-named table if present, otherwise create meal_items.
     */
    public function up(): void
    {
        if (Schema::hasTable('meal_items')) {
            return;
        }

        if (Schema::hasTable('foodwaste_meal_items')) {
            Schema::rename('foodwaste_meal_items', 'meal_items');
            return;
        }

        Schema::create('meal_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meal_log_id')->constrained('foodwast_meal_logs')->onDelete('cascade');
            $table->string('menu_name');
            $table->string('category')->nullable();
            $table->integer('calories')->default(0);
            $table->enum('status', ['verified', 'pending_review'])->default('verified');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        // No-op: we do not want to drop the reconciled table on rollback.
    }
};
