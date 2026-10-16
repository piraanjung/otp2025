<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tabwater_pipe_nodes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('station_id')->comment('FK -> tabwater_stations.id (สถานีต้นทาง)');
            $table->string('node_code', 50)->comment('รหัสจุด เช่น JNC-01');
            $table->string('name')->comment('ชื่อจุดเชื่อมต่อ/จุดกระจายน้ำ');
            $table->enum('type', ['station', 'main_junction', 'sub_junction', 'valve_point', 'tank', 'end_point'])
                  ->default('main_junction')
                  ->comment('ประเภทโหนด');
            $table->decimal('lat', 10, 7)->comment('ละติจูด');
            $table->decimal('lng', 10, 7)->comment('ลองจิจูด');
            $table->text('remark')->nullable(); 
            $table->enum('deleted', ['0','1']);
            $table->timestamps();

            $table->foreign('station_id')->references('id')->on('tabwater_stations')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tabwater_pipe_nodes');
    }
};