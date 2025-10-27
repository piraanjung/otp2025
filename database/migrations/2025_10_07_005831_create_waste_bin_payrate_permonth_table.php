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
        Schema::create('waste_bin_payrate_permonth', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kp_usergroup_idfk')->constrained('kp_usergroups')->onDelete('restrict');
            $table->foreignId('budgetyear_idfk')->constrained('budget_year')->onDelete('restrict');
            $table->float('payrate_permonth', 8,2);
            $table->float('vat', 4,2);
            $table->enum('status',['active', 'inactive'])->default('active');
            $table->tinyInteger('deleted')->default(0);
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
        Schema::dropIfExists('waste_bin_payrate_permonth');
    }
};
