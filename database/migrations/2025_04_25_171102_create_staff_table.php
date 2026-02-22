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
        Schema::create('staffs', function (Blueprint $table) {
            $table->foreignId('id')->constrained('users')->onDelete('cascade')
                ->primary()->unique();
            $table->foreignId('org_id_fk')->constrained('organizations')->onDelete('cascade');
            $table->enum('status', ['active', 'inactive']);
            $table->enum('deleted', ['0','1']);
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
        Schema::dropIfExists('staff');
    }
};
