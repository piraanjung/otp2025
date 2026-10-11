<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('kp_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('org_id_fk');
            $table->string('key_name', 100);
            $table->text('key_value')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();

            $table->unique(['org_id_fk', 'key_name']);
            $table->foreign('org_id_fk')->references('id')->on('organizations')->onDelete('cascade');
        });
    }

    public function down(): void {
        Schema::dropIfExists('kp_settings');
    }
};