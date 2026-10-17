<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tabwater_pipelines', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('station_id')->comment('FK -> tabwater_stations.id');
            $table->string('pipe_code', 50)->comment('รหัสเส้นท่อ เช่น PIPE-M01');
            $table->string('name')->comment('ชื่อสายท่อ');

            // จุดต้นทาง และ จุดปลายทาง
            $table->unsignedBigInteger('from_node_id')->comment('FK -> tabwater_pipe_nodes.id');
            $table->unsignedBigInteger('to_node_id')->comment('FK -> tabwater_pipe_nodes.id');

            // ลำดับชั้นท่อและสเปก
            $table->enum('level', ['main', 'sub_main', 'distribution'])->default('main')->comment('ท่อเมน / ท่อรอง / ท่อซอย');
            $table->integer('size_mm')->default(110)->comment('ขนาดท่อ (มม.) เช่น 110, 63, 32');
            $table->string('material', 50)->default('PE')->comment('ประเภทวัสดุ เช่น PE, PVC, STEEL');
            $table->decimal('length_m', 8, 2)->nullable()->comment('ความยาวท่อ (เมตร)');

            // เก็บพิกัดเส้นทางท่อแบบ JSON [[lat, long], [lat, long], ...]
            $table->json('path_coordinates')->comment('พิกัดแนวท่อผ่าน');
            $table->enum('deleted', ['0','1']);
            $table->timestamps();


            $table->foreign('station_id')->references('id')->on('tabwater_stations')->onDelete('cascade');
            $table->foreign('from_node_id')->references('id')->on('tabwater_pipe_nodes')->onDelete('cascade');
            $table->foreign('to_node_id')->references('id')->on('tabwater_pipe_nodes')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tabwater_pipelines');
    }
};